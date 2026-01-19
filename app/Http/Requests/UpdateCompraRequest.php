<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'proveedor_id' => ['required', 'integer', 'exists:proveedores,id'],
            'fecha_compra' => ['required', 'date'],
            'numero_comprobante' => ['required', 'string', 'max:255'],
            'tipo_comprobante' => ['required', 'in:factura,nota_venta'],

            'detalle_producto_id' => ['required', 'array', 'min:1'],
            'detalle_producto_id.*' => ['nullable', 'integer', 'exists:productos,id'],
            'detalle_cantidad' => ['required', 'array', 'min:1'],
            'detalle_cantidad.*' => ['nullable', 'integer', 'min:1'],
            'detalle_costo_unitario' => ['required', 'array', 'min:1'],
            'detalle_costo_unitario.*' => ['nullable', 'numeric', 'min:0.01'],
        ];
    }
}
