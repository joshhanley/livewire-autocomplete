<?php

namespace LivewireAutocomplete\Tests\Browser;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class DatabaseTest extends TestCase
{
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $databaseFile = __DIR__.'/../database/database.sqlite';

        if (! file_exists($databaseFile)) {
            touch($databaseFile);
        }

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => $databaseFile,
            'prefix' => '',
        ]);

        $app['config']->set('livewire.legacy_model_binding', true);
    }

    public function defaultComponent()
    {
        return new class extends Component
        {
            public $items;

            public $itemName = '';

            public $selectedItem;

            public $rules = [
                'items.*.id' => '',
                'items.*.name' => '',
                'selectedItem' => '',
            ];

            public function mount()
            {
                $this->getItems();
            }

            public function getItems()
            {
                $this->items = Item::query()
                    ->when($this->itemName, function ($query, $itemName) {
                        return $query->where('name', 'LIKE', "%{$itemName}%");
                    })
                    ->get();
            }

            public function updatedItemName()
            {
                $this->reset('items');
                $this->getItems();
            }

            public function updatedSelectedItem($selected)
            {
                $this->selectedItem = Item::find($selected ?? null);
                $this->itemName = $this->selectedItem->name ?? null;
            }

            public function render()
            {
                return <<<'HTML'
                    <div dusk="page">
                        <x-autocomplete wire:model.live="selectedItem">
                            <x-autocomplete.input wire:model.live="itemName" dusk="input" :disabled="(bool) $selectedItem" />
                            @if ($selectedItem)
                                <button type="button" x-on:click="clear" dusk="clear">Clear</button>
                            @endif

                            <x-autocomplete.list dusk="dropdown">
                                @foreach($this->items as $index => $item)
                                    <x-autocomplete.item :key="$item->id" :value="$item->name" dusk="result-{{ $index }}">
                                        <div>{{ $item->name }}</div>
                                    </x-autocomplete.item>
                                @endforeach
                            </x-autocomplete.list>
                        </x-autocomplete>

                        <div dusk="result-output">@if($selectedItem)ID:{{ $selectedItem->id }} - Name:{{ $selectedItem->name }}@endif</div>
                    </div>
                    HTML;
            }
        };
    }

    /** @test */
    public function dropdown_shows_list_of_results()
    {
        Item::truncate();

        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'test4']);
        Item::create(['name' => 'test5']);

        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertSeeInOrder('@dropdown', [
                'test1',
                'test2',
                'test3',
                'test4',
                'test5',
            ]);
    }

    /** @test */
    public function results_are_filtered_based_on_input()
    {
        Item::truncate();

        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'other1']);
        Item::create(['name' => 'other2']);

        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->waitForLivewire()->type('@input', 'o')
            ->assertDontSeeIn('@dropdown', 'test1')
            ->assertDontSeeIn('@dropdown', 'test2')
            ->assertDontSeeIn('@dropdown', 'test3')
            ->assertSeeInOrder('@dropdown', [
                'other1',
                'other2',
            ]);
    }

    /** @test */
    public function ensure_results_count_gets_updated_so_focus_cant_go_off_the_end_of_results()
    {
        Item::truncate();

        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'other1']);
        Item::create(['name' => 'other2']);

        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->keys('@input', '{ARROW_DOWN}')
            ->waitForLivewire()->type('@input', 'o')
            ->assertSeeInOrder('@dropdown', [
                'other1',
                'other2',
            ])
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@result-1', 'bg-blue-500')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@result-1', 'bg-blue-500');
    }

    // /** @test */
    // public function results_dropdown_is_not_shown_if_there_are_no_results_found()
    // {
    //     Item::create(['name' => 'test1']);
    //     Item::create(['name' => 'test2']);
    //     Item::create(['name' => 'test3']);
    //     Item::create(['name' => 'other1']);
    //     Item::create(['name' => 'other2']);

    //     $this->browse(function (Browser $browser) {
    //         Livewire::visit($browser, DatabaseResultsAutocompleteComponent::class)
    //                 ->assertMissing('@dropdown')
    //                 ->click('@input')
    //                 ->waitForLivewire()->type('@input', 'o')
    //                 ->assertSeeInOrder('@dropdown', [
    //                     'other1',
    //                     'other2',
    //                 ])
    //                 ->assertVisible('@dropdown')
    //                 ->waitForLivewire()->type('@input', 'a')
    //                 // Pause to allow transitions to run
    //                 ->pause(101)
    //                 ->assertMissing('@dropdown')
    //                 ;
    //     });
    // }

    /** @test */
    public function selected_item_can_be_cleared()
    {
        Item::truncate();

        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'other1']);
        Item::create(['name' => 'other2']);

        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->waitForLivewire()->click('@result-1')
            ->assertValue('@input', 'test2')
            ->assertSeeIn('@result-output', 'ID:2')
            ->waitForLivewire()->click('@clear')
            ->assertValue('@input', '')
            ->assertSeeNothingIn('@result-output');

        Item::truncate();
    }

    /** @test */
    public function clear_button_cant_be_pressed_if_nothing_selected()
    {
        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'other1']);
        Item::create(['name' => 'other2']);

        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->assertMissing('@clear');
        // ->waitForLivewire()->click('@clear')
    }

    /** @test */
    public function input_cannot_be_focused_when_item_is_selected()
    {
        Item::truncate();

        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'other1']);
        Item::create(['name' => 'other2']);

        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->waitForLivewire()->click('@result-1')
            ->assertValue('@input', 'test2')
            ->assertNotFocused('@input')
            ->click('@input')
            ->assertNotFocused('@input');
    }
}

class Item extends Model
{
    use HasFactory;

    protected $guarded = [];
}
