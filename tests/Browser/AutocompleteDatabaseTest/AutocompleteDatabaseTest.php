<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompleteDatabaseTest;

use Laravel\Dusk\Browser;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\AutocompleteDatabaseTest\Models\Item;
use LivewireAutocomplete\Tests\Browser\TestCase;

class AutocompleteDatabaseTest extends TestCase
{
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom($this->packagePath . '/database/migrations');
    }

    /** @test */
    public function dropdown_shows_list_of_results()
    {
        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'test4']);
        Item::create(['name' => 'test5']);

        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, DatabaseResultsAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertSeeInOrder('@autocomplete-dropdown', [
                        'test1',
                        'test2',
                        'test3',
                        'test4',
                        'test5'
                        ])
                    ;
        });
    }

    /** @test */
    public function results_are_filtered_based_on_input()
    {
        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'other1']);
        Item::create(['name' => 'other2']);

        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, DatabaseResultsAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->waitForLivewire()->type('@autocomplete-input', 'o')
                    ->assertDontSeeIn('@autocomplete-dropdown', 'test1')
                    ->assertDontSeeIn('@autocomplete-dropdown', 'test2')
                    ->assertDontSeeIn('@autocomplete-dropdown', 'test3')
                    ->assertSeeInOrder('@autocomplete-dropdown', [
                        'other1',
                        'other2',
                        ])
                    ;
        });
    }
}
