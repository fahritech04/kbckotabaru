<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class FirestoreService
{
    private ?object $client = null;

    private ?string $lastError = null;

    public function isAvailable(): bool
    {
        try {
            $this->client();

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function lastError(): ?string
    {
        return $this->lastError;
    }

    public function all(string $collection, ?string $orderBy = null, string $direction = 'asc', ?int $limit = null): array
    {
        return $this->read(function () use ($collection, $orderBy, $direction, $limit): array {
            $rows = $this->mapNodes(
                $this->client()->getReference($collection)->getValue()
            );

            if ($orderBy !== null) {
                $rows = $this->sortRows($rows, $orderBy, $direction);
            }

            if ($limit !== null) {
                $rows = array_slice($rows, 0, $limit);
            }

            return $rows;
        }, []);
    }

    public function find(string $collection, string $id): ?array
    {
        return $this->read(function () use ($collection, $id): ?array {
            $snapshot = $this->client()->getReference($this->path($collection, $id))->getSnapshot();

            if (! $snapshot->exists()) {
                return null;
            }

            return ['id' => $id, ...$this->normalizeNode($snapshot->getValue())];
        }, null);
    }

    public function where(
        string $collection,
        string $field,
        mixed $value,
        ?string $orderBy = null,
        string $direction = 'asc',
        ?int $limit = null
    ): array {
        return $this->read(function () use ($collection, $field, $value, $orderBy, $direction, $limit): array {
            $rows = array_values(
                array_filter(
                    $this->all($collection),
                    fn (array $row): bool => ($row[$field] ?? null) === $value
                )
            );

            if ($orderBy !== null) {
                $rows = $this->sortRows($rows, $orderBy, $direction);
            }

            if ($limit !== null) {
                $rows = array_slice($rows, 0, $limit);
            }

            return $rows;
        }, []);
    }

    public function whereFirst(string $collection, string $field, mixed $value): ?array
    {
        $result = $this->where($collection, $field, $value, null, 'asc', 1);

        return $result[0] ?? null;
    }

    public function create(string $collection, array $data, ?string $id = null): array
    {
        return $this->write(function () use ($collection, $data, $id): array {
            if ($id !== null) {
                $identifier = $id;
                $this->client()->getReference($this->path($collection, $identifier))->set($data);

                return $this->find($collection, $identifier) ?? ['id' => $identifier, ...$data];
            }

            $reference = $this->client()->getReference($collection)->push($data);
            $identifier = (string) $reference->getKey();

            return $this->find($collection, $identifier) ?? ['id' => $identifier, ...$data];
        });
    }

    public function update(string $collection, string $id, array $data): ?array
    {
        return $this->write(function () use ($collection, $id, $data): ?array {
            $reference = $this->client()->getReference($this->path($collection, $id));
            $snapshot = $reference->getSnapshot();

            if (! $snapshot->exists()) {
                return null;
            }

            $reference->update($data);

            return $this->find($collection, $id);
        });
    }

    public function delete(string $collection, string $id): bool
    {
        return $this->write(function () use ($collection, $id): bool {
            $reference = $this->client()->getReference($this->path($collection, $id));
            $snapshot = $reference->getSnapshot();

            if (! $snapshot->exists()) {
                return false;
            }

            $reference->remove();

            return true;
        });
    }

    private function client(): object
    {
        if ($this->client !== null) {
            return $this->client;
        }

        try {
            $this->client = app('firebase.database');
            $this->lastError = null;

            return $this->client;
        } catch (Throwable $exception) {
            $this->lastError = $exception->getMessage();

            throw new RuntimeException('Koneksi Firebase gagal. Pastikan kredensial dan FIREBASE_DATABASE_URL sudah benar.', previous: $exception);
        }
    }

    private function mapNodes(?array $nodes): array
    {
        if (! is_array($nodes)) {
            return [];
        }

        $result = [];

        foreach ($nodes as $id => $node) {
            if (! is_array($node)) {
                continue;
            }

            $result[] = ['id' => (string) $id, ...$this->normalizeNode($node)];
        }

        return $result;
    }

    private function normalizeNode(mixed $node): array
    {
        return is_array($node) ? $node : [];
    }

    private function sortRows(array $rows, string $orderBy, string $direction): array
    {
        usort($rows, function (array $left, array $right) use ($orderBy, $direction): int {
            $leftValue = $left[$orderBy] ?? null;
            $rightValue = $right[$orderBy] ?? null;

            if ($leftValue === $rightValue) {
                return 0;
            }

            $comparison = $leftValue <=> $rightValue;

            return strtolower($direction) === 'desc' ? -$comparison : $comparison;
        });

        return $rows;
    }

    private function path(string $collection, string $id): string
    {
        return "{$collection}/{$id}";
    }

    private function read(Closure $callback, mixed $fallback): mixed
    {
        try {
            return $callback();
        } catch (Throwable $exception) {
            $this->lastError = $exception->getMessage();

            Log::warning('Firebase database read error', ['error' => $exception->getMessage()]);

            return $fallback;
        }
    }

    private function write(Closure $callback): mixed
    {
        try {
            $result = $callback();
            $this->lastError = null;

            return $result;
        } catch (Throwable $exception) {
            $this->lastError = $exception->getMessage();

            Log::error('Firebase database write error', ['error' => $exception->getMessage()]);

            throw new RuntimeException('Operasi gagal. Cek koneksi Firebase/kredensial lalu coba lagi.', previous: $exception);
        }
    }
}
