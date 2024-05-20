<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\AutocompleteDatabaseTest;

use Livewire\Livewire;
use LivewireAutocomplete\Tests\LegacyBrowser\AutocompleteDatabaseTest\Models\Item;
use LivewireAutocomplete\Tests\TestCase;

class AutocompleteDatabaseTest extends TestCase
{
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $databaseFile = __DIR__.'/../../database/database.sqlite';

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

    /** @test */
    public function dropdown_shows_list_of_results()
    {
        Item::updateOrCreate(['id' => 1], ['name' => 'test1']);
        Item::updateOrCreate(['id' => 2], ['name' => 'test2']);
        Item::updateOrCreate(['id' => 3], ['name' => 'test3']);
        Item::updateOrCreate(['id' => 4], ['name' => 'test4']);
        Item::updateOrCreate(['id' => 5], ['name' => 'test5']);

        Livewire::visit(DatabaseResultsAutocompleteComponent::class)
            ->click('@autocomplete-input')
                // Pause to allow transitions to run
            ->pause(100)
            ->assertSeeInOrder('@autocomplete-dropdown', [
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
        Item::updateOrCreate(['id' => 1], ['name' => 'test1']);
        Item::updateOrCreate(['id' => 2], ['name' => 'test2']);
        Item::updateOrCreate(['id' => 3], ['name' => 'test3']);
        Item::updateOrCreate(['id' => 4], ['name' => 'other1']);
        Item::updateOrCreate(['id' => 5], ['name' => 'other2']);

        Livewire::visit(DatabaseResultsAutocompleteComponent::class)
            ->click('@autocomplete-input')
            ->waitForLivewire()->type('@autocomplete-input', 'o')
            ->assertDontSeeIn('@autocomplete-dropdown', 'test1')
            ->assertDontSeeIn('@autocomplete-dropdown', 'test2')
            ->assertDontSeeIn('@autocomplete-dropdown', 'test3')
            ->assertSeeInOrder('@autocomplete-dropdown', [
                'other1',
                'other2',
            ]);
    }

    /** @test */
    public function ensure_results_count_gets_updated_so_focus_cant_go_off_the_end_of_results()
    {
        Item::updateOrCreate(['id' => 1], ['name' => 'test1']);
        Item::updateOrCreate(['id' => 2], ['name' => 'test2']);
        Item::updateOrCreate(['id' => 3], ['name' => 'test3']);
        Item::updateOrCreate(['id' => 4], ['name' => 'other1']);
        Item::updateOrCreate(['id' => 5], ['name' => 'other2']);

        Livewire::visit(DatabaseResultsAutocompleteComponent::class)
            ->click('@autocomplete-input')
            ->keys('@autocomplete-input', '{ARROW_DOWN}')
            ->waitForLivewire()->type('@autocomplete-input', 'o')
            ->assertSeeInOrder('@autocomplete-dropdown', [
                'other1',
                'other2',
            ])
            ->keys('@autocomplete-input', '{ARROW_DOWN}')
            ->keys('@autocomplete-input', '{ARROW_DOWN}')
            ->assertHasClass('@result-1', 'bg-blue-500')
            ->keys('@autocomplete-input', '{ARROW_DOWN}')
            ->assertHasClass('@result-1', 'bg-blue-500');
    }

    // /** @test */
    // public function results_dropdown_is_not_shown_if_there_are_no_results_found()
    // {
    //     Item::updateOrCreate(['id' => 1], ['name' => 'test1']);
    //     Item::updateOrCreate(['id' => 2], ['name' => 'test2']);
    //     Item::updateOrCreate(['id' => 3], ['name' => 'test3']);
    //     Item::updateOrCreate(['id' => 4], ['name' => 'other1']);
    //     Item::updateOrCreate(['id' => 5], ['name' => 'other2']);

    //     $this->browse(function (LegacyBrowser $browser) {
    //         Livewire::visit(DatabaseResultsAutocompleteComponent::class)
    //                 ->assertMissing('@autocomplete-dropdown')
    //                 ->click('@autocomplete-input')
    //                 ->waitForLivewire()->type('@autocomplete-input', 'o')
    //                 ->assertSeeInOrder('@autocomplete-dropdown', [
    //                     'other1',
    //                     'other2',
    //                 ])
    //                 ->assertVisible('@autocomplete-dropdown')
    //                 ->waitForLivewire()->type('@autocomplete-input', 'a')
    //                 // Pause to allow transitions to run
    //                 ->pause(101)
    //                 ->assertMissing('@autocomplete-dropdown')
    //                 ;
    //     });
    // }

    /** @test */
    public function selected_item_can_be_cleared()
    {
        Item::updateOrCreate(['id' => 1], ['name' => 'test1']);
        Item::updateOrCreate(['id' => 2], ['name' => 'test2']);
        Item::updateOrCreate(['id' => 3], ['name' => 'test3']);
        Item::updateOrCreate(['id' => 4], ['name' => 'other1']);
        Item::updateOrCreate(['id' => 5], ['name' => 'other2']);

        Livewire::visit(DatabaseResultsAutocompleteComponent::class)
            ->click('@autocomplete-input')
            ->waitForLivewire()->click('@result-1')
            ->assertValue('@autocomplete-input', 'test2')
            ->assertSeeIn('@result-output', 'ID:2')
            ->waitForLivewire()->click('@clear')
            ->assertValue('@autocomplete-input', '')
            ->assertSeeNothingIn('@result-output');
    }

    /** @test */
    public function clear_button_cant_be_pressed_if_nothing_selected()
    {
        Item::updateOrCreate(['id' => 1], ['name' => 'test1']);
        Item::updateOrCreate(['id' => 2], ['name' => 'test2']);
        Item::updateOrCreate(['id' => 3], ['name' => 'test3']);
        Item::updateOrCreate(['id' => 4], ['name' => 'other1']);
        Item::updateOrCreate(['id' => 5], ['name' => 'other2']);

        Livewire::visit(DatabaseResultsAutocompleteComponent::class)
            ->click('@autocomplete-input')
            ->assertMissing('@clear');
        // ->waitForLivewire()->click('@clear')
    }

    /** @test */
    public function input_cannot_be_focused_when_item_is_selected()
    {
        Item::updateOrCreate(['id' => 1], ['name' => 'test1']);
        Item::updateOrCreate(['id' => 2], ['name' => 'test2']);
        Item::updateOrCreate(['id' => 3], ['name' => 'test3']);
        Item::updateOrCreate(['id' => 4], ['name' => 'other1']);
        Item::updateOrCreate(['id' => 5], ['name' => 'other2']);

        Livewire::visit(DatabaseResultsAutocompleteComponent::class)
            ->click('@autocomplete-input')
            ->waitForLivewire()->click('@result-1')
            ->assertValue('@autocomplete-input', 'test2')
            ->assertNotFocused('@autocomplete-input')
            ->click('@autocomplete-input')
            ->assertNotFocused('@autocomplete-input');
    }
}
