<?php

namespace App\Filament\Actions;

use App\Models\Employee;
use App\Services\ClientTransferService;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;

class TransferClientsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'transfer_clients';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Transferir Clientes')
            ->icon('heroicon-o-arrow-path')
            ->color('info')
            ->modalHeading('Transferir Clientes de Empleado')
            ->modalDescription('Selecciona el empleado destino y los clientes que deseas transferir.')
            ->modalWidth(MaxWidth::ThreeExtraLarge)
            ->visible(function () {
                return $this->getRecord()->clients()->count() > 0;
            })
            ->form(function (Form $form) {
                return $form->schema([
                    Grid::make()
                        ->schema([
                            Grid::make()
                                ->schema([
                                    Section::make('Empleado Origen')
                                        ->description('Empleado que cederá los clientes.')
                                        ->schema([
                                            \Filament\Forms\Components\Placeholder::make('source_employee_info')
                                                ->label('')
                                                ->content(function () {
                                                    $employee = $this->getRecord();
                                                    $clientsCount = $employee->clients()->count();
                                                    $branchInfo = $employee->branch ? " - {$employee->branch->name}" : '';
                                                    return "**{$employee->full_name}**{$branchInfo}\n📊 **{$clientsCount}** cliente(s) asignado(s)";
                                                })
                                                ->columnSpanFull(),
                                        ])
                                        ->collapsible()
                                        ->collapsed(),

                                    Section::make('Configuración de Transferencia')
                                        ->description('Selecciona el empleado destino y el tipo de transferencia.')
                                        ->schema([
                                            Select::make('target_employee_id')
                                                ->label('Empleado Destino')
                                                ->placeholder('Selecciona el empleado que recibirá los clientes')
                                                ->options(function () {
                                                    $currentEmployeeId = $this->getRecord()->id;
                                                    return Employee::where('id', '!=', $currentEmployeeId)
                                                        ->with('branch')
                                                        ->get()
                                                        ->mapWithKeys(function ($employee) {
                                                            $branchInfo = $employee->branch ? " - {$employee->branch->name}" : '';
                                                            return [$employee->id => $employee->full_name . $branchInfo];
                                                        });
                                                })
                                                ->searchable()
                                                ->required()
                                                ->helperText('El empleado que recibirá los clientes transferidos.')
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    if ($state) {
                                                        $targetEmployee = Employee::find($state);
                                                        $set('target_employee_info', $targetEmployee ? $targetEmployee->full_name : '');
                                                    }
                                                }),

                                            \Filament\Forms\Components\Placeholder::make('target_info')
                                                ->label('Información del Empleado Destino')
                                                ->content(function (callable $get) {
                                                    $targetId = $get('target_employee_id');
                                                    if (!$targetId) return '👤 Selecciona un empleado para ver su información.';

                                                    $targetEmployee = Employee::with('branch')->find($targetId);
                                                    if (!$targetEmployee) return '❌ Empleado no encontrado.';

                                                    $currentClients = $targetEmployee->clients()->count();
                                                    $branchInfo = $targetEmployee->branch ? " - {$targetEmployee->branch->name}" : '';

                                                    return "**{$targetEmployee->full_name}**{$branchInfo}\n📊 Clientes actuales: **{$currentClients}**";
                                                })
                                                ->visible(fn(callable $get) => (bool) $get('target_employee_id')),

                                            Toggle::make('transfer_all')
                                                ->label('Transferir TODOS los clientes')
                                                ->helperText('💡 Activa esta opción para transferir todos los clientes sin selección individual.')
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    if ($state) {
                                                        $set('selected_clients', []);
                                                    }
                                                }),
                                        ]),
                                ])
                                ->columnSpan(1),

                            Grid::make()
                                ->schema([
                                    Section::make('Selección de Clientes')
                                        ->description('')
                                        ->schema([
                                            \Filament\Forms\Components\TextInput::make('client_search')
                                                ->label('🔍 Buscar Clientes')
                                                ->placeholder('Buscar por nombre, negocio o teléfono...')
                                                ->live(debounce: 500)
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    // Limpiar selección cuando se busca
                                                    if (!empty($state)) {
                                                        $set('selected_clients', []);
                                                    }
                                                })
                                                ->helperText('💡 Escribe para buscar clientes específicos')
                                                ->visible(fn(callable $get) => !$get('transfer_all')),

                                            CheckboxList::make('selected_clients')
                                                ->label('Clientes a Transferir')
                                                ->options(function (callable $get) {
                                                    $searchTerm = $get('client_search') ?? '';
                                                    
                                                    if (!empty($searchTerm)) {
                                                        // Si hay búsqueda, mostrar resultados filtrados
                                                        return $this->getRecord()
                                                            ->clients()
                                                            ->where(function ($query) use ($searchTerm) {
                                                                $query->where('first_name', 'like', "%{$searchTerm}%")
                                                                    ->orWhere('last_name', 'like', "%{$searchTerm}%")
                                                                    ->orWhere('business_name', 'like', "%{$searchTerm}%")
                                                                    ->orWhere('phone_number', 'like', "%{$searchTerm}%");
                                                            })
                                                            ->orderBy('first_name')
                                                            ->limit(20)
                                                            ->get()
                                                            ->mapWithKeys(function ($client) {
                                                                $businessName = $client->business_name ? " ({$client->business_name})" : '';
                                                                return [$client->id => $client->first_name . ' ' . $client->last_name . $businessName];
                                                            });
                                                    }
                                                    
                                                    // Por defecto, mostrar solo los primeros 3
                                                    $totalClients = $this->getRecord()->clients()->count();
                                                    $clients = $this->getRecord()
                                                        ->clients()
                                                        ->orderBy('first_name')
                                                        ->limit(3)
                                                        ->get()
                                                        ->mapWithKeys(function ($client) {
                                                            $businessName = $client->business_name ? " ({$client->business_name})" : '';
                                                            return [$client->id => $client->first_name . ' ' . $client->last_name . $businessName];
                                                        });
                                                    
                                                    if ($totalClients > 3) {
                                                        $clients['__more_info__'] = "... y " . ($totalClients - 3) . " cliente(s) más (usa la búsqueda arriba)";
                                                    }
                                                    
                                                    return $clients;
                                                })
                                                ->columns(1)
                                                ->visible(fn(callable $get) => !$get('transfer_all'))
                                                ->required(fn(callable $get) => !$get('transfer_all'))
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    if (is_array($state)) {
                                                        // Filtrar el elemento informativo
                                                        $realClients = array_filter($state, function($id) {
                                                            return $id !== '__more_info__';
                                                        });
                                                        $count = count($realClients);
                                                        $set('selected_clients', array_values($realClients));
                                                    } else {
                                                        $count = 0;
                                                    }
                                                    $set('selected_count', $count);
                                                }),

                                            \Filament\Forms\Components\Placeholder::make('selection_summary')
                                                ->label('')
                                                ->content(function (callable $get) {
                                                    if ($get('transfer_all')) {
                                                        $total = $this->getRecord()->clients()->count();
                                                        return "**✅ Se transferirán TODOS los {$total} cliente(s).**";
                                                    }

                                                    $selected = $get('selected_clients');
                                                    if (is_array($selected)) {
                                                        // Filtrar elementos informativos
                                                        $realClients = array_filter($selected, function($id) {
                                                            return $id !== '__more_info__';
                                                        });
                                                        $count = count($realClients);
                                                        if ($count > 0) {
                                                            return "**✅ Se transferirán {$count} cliente(s) seleccionado(s).**";
                                                        }
                                                    }
                                                    
                                                    return '';
                                                })
                                                ->visible(function (callable $get) {
                                                    if ($get('transfer_all')) {
                                                        return true;
                                                    }
                                                    
                                                    $selected = $get('selected_clients');
                                                    if (is_array($selected)) {
                                                        $realClients = array_filter($selected, function($id) {
                                                            return $id !== '__more_info__';
                                                        });
                                                        return count($realClients) > 0;
                                                    }
                                                    
                                                    return false;
                                                }),
                                        ])
                                        ->visible(fn(callable $get) => !$get('transfer_all')),
                                ])
                                ->columnSpan(1)
                        ])
                        ->columns(2),
                ]);
            })
            ->action(function (array $data) {
                $this->transferClients($data);
            })
            ->modalFooterActionsAlignment('end')
            ->modalSubmitActionLabel('Transferir Clientes');
    }

    protected function transferClients(array $data): void
    {
        $transferService = new ClientTransferService();

        $sourceEmployee = $this->getRecord();
        $targetEmployee = Employee::findOrFail($data['target_employee_id']);

        // Filtrar elementos informativos de la selección
        $clientIds = [];
        if (isset($data['selected_clients']) && is_array($data['selected_clients'])) {
            $clientIds = array_filter($data['selected_clients'], function($id) {
                return $id !== '__more_info__' && is_numeric($id);
            });
        }

        $result = $transferService->transferClients(
            sourceEmployee: $sourceEmployee,
            targetEmployee: $targetEmployee,
            clientIds: array_values($clientIds),
            transferAll: $data['transfer_all'] ?? false,
        );

        if ($result['success']) {
            Notification::make()
                ->title('¡Transferencia completada!')
                ->body($result['message'])
                ->success()
                ->duration(5000)
                ->send();
        } else {
            Notification::make()
                ->title('Error en la transferencia')
                ->body($result['message'])
                ->danger()
                ->duration(5000)
                ->send();
        }
    }
}
