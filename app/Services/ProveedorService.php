<?php

namespace App\Services;

use App\Models\Proveedor;
use App\Repositories\ProveedorRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProveedorService
{
    public function __construct(
        private readonly ProveedorRepository $proveedorRepository,
    ) {
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->proveedorRepository->paginate($filters, $perPage);
    }

    public function listActive(): Collection
    {
        return $this->proveedorRepository->listActive();
    }

    public function create(array $data): array
    {
        $data['estado'] = $data['estado'] ?? 'activo';

        if ($this->proveedorRepository->existsByIdentificacion($data['identificacion'])) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'identificacion',
                'message' => 'Sistema notifica y no registra.',
            ];
        }

        if ($this->proveedorRepository->existsByCorreo($data['correo'] ?? null)) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'correo',
                'message' => 'Sistema notifica y no registra.',
            ];
        }

        $proveedor = $this->proveedorRepository->create($data);

        return [
            'ok' => true,
            'proveedor' => $proveedor,
        ];
    }

    public function update(int $id, array $data): array
    {
        $proveedor = $this->proveedorRepository->findById($id);

        if ($proveedor === null) {
            return [
                'ok' => false,
                'error' => 'not_found',
                'message' => 'No se encontró el registro solicitado.',
            ];
        }

        if ($this->proveedorRepository->existsByIdentificacion($data['identificacion'], $proveedor->id)) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'identificacion',
                'message' => 'Sistema notifica y no registra.',
            ];
        }

        if ($this->proveedorRepository->existsByCorreo($data['correo'] ?? null, $proveedor->id)) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'correo',
                'message' => 'Sistema notifica y no registra.',
            ];
        }

        $proveedor = $this->proveedorRepository->update($proveedor, $data);

        return [
            'ok' => true,
            'proveedor' => $proveedor,
        ];
    }

    public function inactivate(int $id): array
    {
        $proveedor = $this->proveedorRepository->findById($id);

        if ($proveedor === null) {
            return [
                'ok' => false,
                'error' => 'not_found',
                'message' => 'No se encontró el registro solicitado.',
            ];
        }

        if ($proveedor->estado !== 'inactivo') {
            $proveedor->estado = 'inactivo';
            $proveedor->save();
        }

        return [
            'ok' => true,
            'proveedor' => $proveedor,
        ];
    }

    public function find(int $id): ?Proveedor
    {
        return $this->proveedorRepository->findById($id);
    }
}
