<?php

declare(strict_types=1);

$root = realpath(__DIR__ . '/..');
if ($root === false) {
    fwrite(STDERR, "No root" . PHP_EOL);
    exit(1);
}

function countFileLines(string $absPath): int
{
    if (!is_file($absPath)) {
        return 0;
    }

    $lines = file($absPath, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        return 0;
    }

    return count($lines);
}

function methodRanges(string $absPath): array
{
    if (!is_file($absPath)) {
        return [];
    }

    $code = file_get_contents($absPath);
    if ($code === false) {
        return [];
    }

    $tokens = token_get_all($code);

    $ranges = [];
    $count = count($tokens);
    $currentLine = 1;

    for ($i = 0; $i < $count; $i++) {
        $t = $tokens[$i];

        if (is_array($t)) {
            $currentLine = $t[2];

            if ($t[0] !== T_FUNCTION) {
                continue;
            }

            // Find method name
            $name = null;
            for ($j = $i + 1; $j < $count; $j++) {
                $tj = $tokens[$j];
                if (is_array($tj) && $tj[0] === T_STRING) {
                    $name = $tj[1];
                    break;
                }
            }

            if ($name === null) {
                continue;
            }

            $startLine = $t[2];

            // Find opening brace '{'
            $braceDepth = 0;
            $foundBody = false;
            $endLine = $startLine;

            for ($k = $j; $k < $count; $k++) {
                $tk = $tokens[$k];

                if (is_array($tk)) {
                    $endLine = $tk[2] + substr_count($tk[1], "\n");
                } else {
                    $endLine += substr_count((string) $tk, "\n");
                }

                $text = is_array($tk) ? $tk[1] : (string) $tk;
                $len = strlen($text);
                for ($c = 0; $c < $len; $c++) {
                    $ch = $text[$c];
                    if ($ch === '{') {
                        $braceDepth++;
                        $foundBody = true;
                    } elseif ($ch === '}' && $foundBody) {
                        $braceDepth--;
                        if ($braceDepth === 0) {
                            $ranges[$name] = [$startLine, $endLine];
                            $i = $k;
                            break 2;
                        }
                    }
                }
            }

            continue;
        }

        $currentLine += substr_count((string) $t, "\n");
    }

    return $ranges;
}

function methodLoc(string $absPath, string $method): int
{
    $ranges = methodRanges($absPath);
    if (!isset($ranges[$method])) {
        return 0;
    }

    [$start, $end] = $ranges[$method];
    return max(0, $end - $start + 1);
}

function sum(array $nums): int
{
    return array_sum(array_map('intval', $nums));
}

function absPath(string $root, string $rel): string
{
    return $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rel);
}

$paths = [
    'cliente' => [
        'controller' => 'app/Http/Controllers/ClienteController.php',
        'service' => 'app/Services/ClienteService.php',
        'repo' => 'app/Repositories/ClienteRepository.php',
        'store_request' => 'app/Http/Requests/StoreClienteRequest.php',
        'update_request' => 'app/Http/Requests/UpdateClienteRequest.php',
        'views' => [
            'index' => 'resources/views/clientes/index.blade.php',
            'create' => 'resources/views/clientes/create.blade.php',
            'edit' => 'resources/views/clientes/edit.blade.php',
            'show' => 'resources/views/clientes/show.blade.php',
        ],
    ],
    'producto' => [
        'controller' => 'app/Http/Controllers/ProductoController.php',
        'service' => 'app/Services/ProductoService.php',
        'repo' => 'app/Repositories/ProductoRepository.php',
        'store_request' => 'app/Http/Requests/StoreProductoRequest.php',
        'update_request' => 'app/Http/Requests/UpdateProductoRequest.php',
        'views' => [
            'index' => 'resources/views/productos/index.blade.php',
            'create' => 'resources/views/productos/create.blade.php',
            'edit' => 'resources/views/productos/edit.blade.php',
            'show' => 'resources/views/productos/show.blade.php',
        ],
    ],
    'proveedor' => [
        'controller' => 'app/Http/Controllers/ProveedorController.php',
        'service' => 'app/Services/ProveedorService.php',
        'repo' => 'app/Repositories/ProveedorRepository.php',
        'store_request' => 'app/Http/Requests/StoreProveedorRequest.php',
        'update_request' => 'app/Http/Requests/UpdateProveedorRequest.php',
        'views' => [
            'index' => 'resources/views/proveedores/index.blade.php',
            'create' => 'resources/views/proveedores/create.blade.php',
            'edit' => 'resources/views/proveedores/edit.blade.php',
            'show' => 'resources/views/proveedores/show.blade.php',
        ],
    ],
    'compra' => [
        'controller' => 'app/Http/Controllers/CompraController.php',
        'service' => 'app/Services/CompraService.php',
        'repo' => 'app/Repositories/CompraRepository.php',
        'store_request' => 'app/Http/Requests/StoreCompraRequest.php',
        'update_request' => 'app/Http/Requests/UpdateCompraRequest.php',
        'views' => [
            'index' => 'resources/views/compras/index.blade.php',
            'create' => 'resources/views/compras/create.blade.php',
            'edit' => 'resources/views/compras/edit.blade.php',
            'show' => 'resources/views/compras/show.blade.php',
        ],
        'extra' => [
            'producto_service' => 'app/Services/ProductoService.php',
            'producto_repo' => 'app/Repositories/ProductoRepository.php',
            'proveedor_service' => 'app/Services/ProveedorService.php',
            'proveedor_repo' => 'app/Repositories/ProveedorRepository.php',
        ],
    ],
];

$useCases = [
    'crear' => function (string $m, array $p, string $root): array {
        $c = absPath($root, $p['controller']);
        $s = absPath($root, $p['service']);
        $r = absPath($root, $p['repo']);

        $controller = sum([
            methodLoc($c, 'create'),
            methodLoc($c, 'store'),
        ]);

        $service = methodLoc($s, 'create');
        $repo = 0;
        if ($m === 'compra') {
            $repo = sum([
                methodLoc($r, 'existsComprobante'),
            ]);
            $service += methodLoc($s, 'normalizeDetalle');

            $ps = absPath($root, $p['extra']['producto_service']);
            $pr = absPath($root, $p['extra']['producto_repo']);
            $vs = absPath($root, $p['extra']['proveedor_service']);
            $vr = absPath($root, $p['extra']['proveedor_repo']);

            $service += methodLoc($ps, 'listActive');
            $repo += methodLoc($pr, 'listActive');
            $service += methodLoc($vs, 'listActive');
            $repo += methodLoc($vr, 'listActive');
        }

        if ($m !== 'compra') {
            $repo = sum([
                methodLoc($r, 'create'),
            ]);
            if ($m === 'cliente') {
                $repo += sum([
                    methodLoc($r, 'existsByIdentificacion'),
                    methodLoc($r, 'existsByCorreo'),
                ]);
            }
            if ($m === 'producto') {
                $repo += methodLoc($r, 'existsByCodigo');
            }
            if ($m === 'proveedor') {
                $repo += sum([
                    methodLoc($r, 'existsByIdentificacion'),
                    methodLoc($r, 'existsByCorreo'),
                ]);
            }
        }

        $requests = countFileLines(absPath($root, $p['store_request']));
        $views = 0;
        if (isset($p['views']['create'])) {
            $views += countFileLines(absPath($root, $p['views']['create']));
        }

        return [
            'controller' => $controller,
            'service' => $service,
            'repo' => $repo,
            'requests' => $requests,
            'views' => $views,
            'total' => sum([$controller, $service, $repo, $requests, $views]),
        ];
    },

    'modificar' => function (string $m, array $p, string $root): array {
        $c = absPath($root, $p['controller']);
        $s = absPath($root, $p['service']);
        $r = absPath($root, $p['repo']);

        $controller = sum([
            methodLoc($c, 'edit'),
            methodLoc($c, 'update'),
        ]);

        $service = methodLoc($s, 'update');
        $repo = 0;

        if ($m === 'compra') {
            $service += methodLoc($s, 'normalizeDetalle');
            $repo += methodLoc($r, 'existsComprobante');

            $ps = absPath($root, $p['extra']['producto_service']);
            $pr = absPath($root, $p['extra']['producto_repo']);
            $vs = absPath($root, $p['extra']['proveedor_service']);
            $vr = absPath($root, $p['extra']['proveedor_repo']);

            $service += methodLoc($ps, 'listActive');
            $repo += methodLoc($pr, 'listActive');
            $service += methodLoc($vs, 'listActive');
            $repo += methodLoc($vr, 'listActive');
        } else {
            $repo += methodLoc($r, 'update');

            if ($m === 'cliente') {
                $repo += sum([
                    methodLoc($r, 'findById'),
                    methodLoc($r, 'existsByIdentificacion'),
                    methodLoc($r, 'existsByCorreo'),
                ]);
            }
            if ($m === 'producto') {
                $repo += sum([
                    methodLoc($r, 'findById'),
                    methodLoc($r, 'existsByCodigo'),
                ]);
            }
            if ($m === 'proveedor') {
                $repo += sum([
                    methodLoc($r, 'findById'),
                    methodLoc($r, 'existsByIdentificacion'),
                    methodLoc($r, 'existsByCorreo'),
                ]);
            }
        }

        $requests = countFileLines(absPath($root, $p['update_request']));
        $views = 0;
        if (isset($p['views']['edit'])) {
            $views += countFileLines(absPath($root, $p['views']['edit']));
        }

        return [
            'controller' => $controller,
            'service' => $service,
            'repo' => $repo,
            'requests' => $requests,
            'views' => $views,
            'total' => sum([$controller, $service, $repo, $requests, $views]),
        ];
    },

    'eliminar' => function (string $m, array $p, string $root): array {
        $c = absPath($root, $p['controller']);
        $s = absPath($root, $p['service']);
        $r = absPath($root, $p['repo']);

        $controller = methodLoc($c, 'destroy');

        if ($m === 'compra') {
            $service = methodLoc($s, 'anular');
            $repo = methodLoc($r, 'findById');
        } else {
            $service = methodLoc($s, 'inactivate');
            $repo = methodLoc($r, 'findById');
        }

        return [
            'controller' => $controller,
            'service' => $service,
            'repo' => $repo,
            'requests' => 0,
            'views' => 0,
            'total' => sum([$controller, $service, $repo]),
        ];
    },

    'consulta_general' => function (string $m, array $p, string $root): array {
        $c = absPath($root, $p['controller']);
        $s = absPath($root, $p['service']);
        $r = absPath($root, $p['repo']);

        $controller = methodLoc($c, 'index');
        $service = methodLoc($s, 'paginate');
        $repo = methodLoc($r, 'paginate');
        $views = countFileLines(absPath($root, $p['views']['index']));

        return [
            'controller' => $controller,
            'service' => $service,
            'repo' => $repo,
            'requests' => 0,
            'views' => $views,
            'total' => sum([$controller, $service, $repo, $views]),
        ];
    },

    'consulta_parametro' => function (string $m, array $p, string $root): array {
        $c = absPath($root, $p['controller']);
        $s = absPath($root, $p['service']);
        $r = absPath($root, $p['repo']);

        $controller = methodLoc($c, 'show');
        $service = methodLoc($s, 'find');
        $repo = methodLoc($r, 'findById');
        $views = countFileLines(absPath($root, $p['views']['show']));

        return [
            'controller' => $controller,
            'service' => $service,
            'repo' => $repo,
            'requests' => 0,
            'views' => $views,
            'total' => sum([$controller, $service, $repo, $views]),
        ];
    },
];

$report = [];
foreach ($paths as $module => $p) {
    foreach ($useCases as $uc => $fn) {
        $report[$module][$uc] = $fn($module, $p, $root);
    }
}

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
