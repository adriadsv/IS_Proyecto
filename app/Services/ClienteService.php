<?php

namespace App\Services;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClienteService
{
    public function __construct(
        private readonly ClienteRepository $clienteRepository,
    ) {
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->clienteRepository->paginate($filters, $perPage);
    }

    public function create(array $data): array
    {
        $data['estado'] = $data['estado'] ?? 'activo';

        if ($this->clienteRepository->existsByIdentificacion($data['identificacion'])) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'identificacion',
                'message' => 'Sistema notifica y no registra.',
            ];
        }

        if ($this->clienteRepository->existsByCorreo($data['correo'] ?? null)) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'correo',
                'message' => 'Sistema notifica y no registra.',
            ];
        }

        $cliente = $this->clienteRepository->create($data);

        return [
            'ok' => true,
            'cliente' => $cliente,
        ];
    }

    public function update(int $id, array $data): array
    {
        $cliente = $this->clienteRepository->findById($id);

        if ($cliente === null) {
            return [
                'ok' => false,
                'error' => 'not_found',
                'message' => 'No se encontró el registro solicitado.',
            ];
        }

        if ($this->clienteRepository->existsByIdentificacion($data['identificacion'], $cliente->id)) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'identificacion',
                'message' => 'Sistema notifica y no registra.',
            ];
        }

        if ($this->clienteRepository->existsByCorreo($data['correo'] ?? null, $cliente->id)) {
            return [
                'ok' => false,
                'error' => 'duplicate',
                'field' => 'correo',
                'message' => 'Sistema notifica y no registra.',
            ];
        }

        $cliente = $this->clienteRepository->update($cliente, $data);

        return [
            'ok' => true,
            'cliente' => $cliente,
        ];
    }

    public function inactivate(int $id): array
    {
        $cliente = $this->clienteRepository->findById($id);

        if ($cliente === null) {
            return [
                'ok' => false,
                'error' => 'not_found',
                'message' => 'No se encontró el registro solicitado.',
            ];
        }

        if ($cliente->estado !== 'inactivo') {
            $cliente->estado = 'inactivo';
            $cliente->save();
        }

        return [
            'ok' => true,
            'cliente' => $cliente,
        ];
    }

    public function find(int $id): ?Cliente
    {
        return $this->clienteRepository->findById($id);
    }
}
