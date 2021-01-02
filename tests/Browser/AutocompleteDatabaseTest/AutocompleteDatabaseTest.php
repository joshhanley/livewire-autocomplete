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

    /** @test */
    public function ensure_results_count_gets_updated_so_focus_cant_go_off_the_end_of_results()
    {
        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'other1']);
        Item::create(['name' => 'other2']);

        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, DatabaseResultsAutocompleteComponent::class)
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
                    ->assertHasClass('@result-1', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function results_dropdown_is_not_shown_if_there_are_no_results_found()
    {
        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'other1']);
        Item::create(['name' => 'other2']);

        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, DatabaseResultsAutocompleteComponent::class)
                    ->assertMissing('@autocomplete-dropdown')
                    ->click('@autocomplete-input')
                    ->waitForLivewire()->type('@autocomplete-input', 'o')
                    ->assertSeeInOrder('@autocomplete-dropdown', [
                        'other1',
                        'other2',
                        ])
                    ->assertVisible('@autocomplete-dropdown')
                    ->waitForLivewire()->type('@autocomplete-input', 'a')
                    ->assertMissing('@autocomplete-dropdown')
                    ;
        });
    }

    /** @test */
    public function selected_item_can_be_cleared()
    {
        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'other1']);
        Item::create(['name' => 'other2']);

        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, DatabaseResultsAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->waitForLivewire()->click('@result-1')
                    ->assertValue('@autocomplete-input', 'test2')
                    ->assertSeeIn('@result-output', '"id":2')
                    ->waitForLivewire()->click('@clear')
                    ->assertValue('@autocomplete-input', '')
                    ->assertSeeNothingIn('@result-output')
                    ;
        });
    }

    /** @test */
    public function clear_button_cant_be_pressed_if_nothing_selected()
    {
        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'other1']);
        Item::create(['name' => 'other2']);

        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, DatabaseResultsAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertMissing('@clear')
                    // ->waitForLivewire()->click('@clear')
                    ;
        });
    }

    /** @test */
    public function input_cannot_be_focused_when_item_is_selected()
    {
        Item::create(['name' => 'test1']);
        Item::create(['name' => 'test2']);
        Item::create(['name' => 'test3']);
        Item::create(['name' => 'other1']);
        Item::create(['name' => 'other2']);

        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, DatabaseResultsAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->waitForLivewire()->click('@result-1')
                    ->assertValue('@autocomplete-input', 'test2')
                    ->assertNotFocused('@autocomplete-input')
                    ->click('@autocomplete-input')
                    ->assertNotFocused('@autocomplete-input')
                    ;
        });
    }
}
