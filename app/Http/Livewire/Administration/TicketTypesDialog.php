<?php

namespace App\Http\Livewire\Administration;

use App\Models\Icon;
use App\Models\TicketType;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Livewire\Component;
use Closure;

class TicketTypesDialog extends Component implements HasForms
{
    use InteractsWithForms;

    public TicketType $type;
    public bool $deleteConfirmationOpened = false;

    protected $listeners = ['doDeleteType', 'cancelDeleteType'];

    public function mount(): void {
        $this->form->fill([
            'title' => $this->type->title,
            'text_color' => $this->type->text_color,
            'bg_color' => $this->type->bg_color,
            'icon' => $this->type->icon,
        ]);
    }


    public function render()
    {
        return view('livewire.administration.ticket-types-dialog');
    }

    /**
     * Form schema definition
     *
     * @return array
     */
    protected function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->label(__('Title'))
                ->maxLength(255)
                ->unique(table: TicketType::class, column: 'title', ignorable: fn () => $this->type, callback: function (Unique $rule) {
                    return $rule->withoutTrashed();
                })
                ->required(),

            ColorPicker::make('text_color')
                ->label(__('Text color'))
                ->required(),

            ColorPicker::make('bg_color')
                ->label(__('Background color'))
                ->required(),

            Select::make('icon')
                ->label(__('Icon'))
                ->reactive()
                ->searchable()
                ->required()
                ->getSearchResultsUsing(fn (string $search) => Icon::where('icon', 'like', "%{$search}%")->limit(50)->pluck('icon', 'icon'))
                ->getOptionLabelUsing(fn ($value): ?string => Icon::where('icon', $value)->first()?->icon)
                ->hint(fn (Closure $get) => $get('icon') ? new HtmlString(__('Selected icon:') . ' <i class="fa fa-2x ' . $get('icon') . '"></i>') : '')
                ->helperText(new HtmlString(__("Check the <a href='https://fontawesome.com/icons' target='_blank' class='text-blue-500 underline'>fontawesome icons here</a> to choose your right icon"))),
        ];
    }

    /**
     * Create / Update the type
     *
     * @return void
     */
    public function save(): void {
        $data = $this->form->getState();
        if (!$this->type?->id) {
            $type = TicketType::create([
                'title' => $data['title'],
                'text_color' => $data['text_color'],
                'bg_color' => $data['bg_color'],
                'icon' => $data['icon'],
                'slug' => Str::slug($data['title'], '_')
            ]);
            Notification::make()
                ->success()
                ->title(__('Type created'))
                ->body(__('The type has been created'))
                ->send();
        } else {
            $this->type->title = $data['title'];
            $this->type->text_color = $data['text_color'];
            $this->type->bg_color = $data['bg_color'];
            $this->type->icon = $data['icon'];
            $this->type->slug = Str::slug($data['title'], '_');
            $this->type->save();
            Notification::make()
                ->success()
                ->title(__('Type updated'))
                ->body(__('The type\'s details has been updated'))
                ->send();
        }
        $this->emit('typeSaved');
    }

    /**
     * Delete an existing type
     *
     * @return void
     */
    public function doDeleteType(): void {
        $this->type->delete();
        $this->deleteConfirmationOpened = false;
        $this->emit('typeDeleted');
        Notification::make()
            ->success()
            ->title(__('Type deleted'))
            ->body(__('The type has been deleted'))
            ->send();
    }

    /**
     * Cancel the deletion of a type
     *
     * @return void
     */
    public function cancelDeleteType(): void {
        $this->deleteConfirmationOpened = false;
    }

    /**
     * Show the delete type confirmation dialog
     *
     * @return void
     * @throws \Exception
     */
    public function deleteType(): void {
        $this->deleteConfirmationOpened = true;
        Notification::make()
            ->warning()
            ->title(__('Type deletion'))
            ->body(__('Are you sure you want to delete this type?'))
            ->actions([
                Action::make('confirm')
                    ->label(__('Confirm'))
                    ->color('danger')
                    ->button()
                    ->close()
                    ->emit('doDeleteType'),
                Action::make('cancel')
                    ->label(__('Cancel'))
                    ->close()
                    ->emit('cancelDeleteType')
            ])
            ->persistent()
            ->send();
    }
}
