<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Component;
use Livewire\Livewire;

class CustomStylesTest extends BrowserTestCase
{
    public function component()
    {
        return new class extends Component
        {
            protected $queryString = [
                'inline',
            ];

            public $inline = false;

            public $results = [
                [
                    'id' => '1',
                    'text' => 'bob',
                ],
                [
                    'id' => '2',
                    'text' => 'john',
                ],
                [
                    'id' => '3',
                    'text' => 'bill',
                ],
            ];

            public $inputText = '';

            public $selectedSlug;

            public function render()
            {
                return <<<'HTML'
                    <div dusk="page">
                        <x-autocomplete wire:model.live="selectedSlug">
                            <x-autocomplete-input wire:model.live="inputText" dusk="input" />

                            <x-autocomplete-list :inline="$inline" dusk="dropdown">
                                @foreach($this->results as $index => $result)
                                    <x-autocomplete-item :key="$result['id']" :value="$result['text']" dusk="result-{{ $index }}">
                                        {{ $result['text'] }}
                                    </x-autocomplete-item>
                                @endforeach
                            </x-autocomplete-list>
                        </x-autocomplete>

                        <div dusk="input-text-output">{{ $inputText }}</div>
                        <div dusk="selected-slug-output">{{ $selectedSlug }}</div>
                    </div>
                    HTML;
            }
        };
    }

    public function componentWithCustomStyles()
    {
        return new class extends Component
        {
            protected $queryString = [
                'inline',
            ];

            public $inline = false;

            public $results = [
                [
                    'id' => '1',
                    'text' => 'bob',
                ],
                [
                    'id' => '2',
                    'text' => 'john',
                ],
                [
                    'id' => '3',
                    'text' => 'bill',
                ],
            ];

            public $inputText = '';

            public $selectedSlug;

            public function render()
            {
                return <<<'HTML'
                    <div dusk="page">
                        <x-autocomplete wire:model.live="selectedSlug">
                            <x-autocomplete-input wire:model.live="inputText" dusk="input" />

                            <x-autocomplete-list :inline="$inline" containerClass="some-style" dusk="dropdown" unstyled>
                                @foreach($this->results as $index => $result)
                                    <x-autocomplete-item active="some-focus-style" :key="$result['id']" :value="$result['text']" dusk="result-{{ $index }}">
                                        {{ $result['text'] }}
                                    </x-autocomplete-item>
                                @endforeach
                            </x-autocomplete-list>
                        </x-autocomplete>

                        <div dusk="input-text-output">{{ $inputText }}</div>
                        <div dusk="selected-slug-output">{{ $selectedSlug }}</div>
                    </div>
                    HTML;
            }
        };
    }

    /** @test */
    public function default_inline_styles_are_used_when_inline_is_true()
    {
        Livewire::withQueryParams(['inline' => true])
            ->visit($this->component())
            ->click('@input')
            // Get the wrapper div around the dropdown list
            ->assertClassMissing('div:has(> [dusk="dropdown"])', 'absolute')
        ;
    }

    /** @test */
    public function default_overlay_styles_are_used_when_inline_is_false()
    {
        Livewire::withQueryParams(['inline' => false])
            ->visit($this->component())
            ->click('@input')
            // Get the wrapper div around the dropdown list
            ->assertHasClass('div:has(> [dusk="dropdown"])', 'absolute')
        ;
    }

    /** @test */
    public function default_result_focus_styles_are_used()
    {
        Livewire::visit($this->component())
            ->click('@input')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@result-0', 'bg-blue-500')
        ;
    }

    /** @test */
    public function custom_inline_styles_are_used_when_inline_is_true()
    {
        Livewire::withQueryParams(['inline' => true])
            ->visit($this->componentWithCustomStyles())
            ->click('@input')
            // Get the wrapper div around the dropdown list
            ->assertHasClass('div:has(> [dusk="dropdown"])', 'some-style')
            ->assertClassMissing('div:has(> [dusk="dropdown"])', 'absolute')
            ->assertClassMissing('div:has(> [dusk="dropdown"])', 'w-full')
        ;
    }

    /** @test */
    public function custom_overlay_styles_are_used_when_inline_is_false()
    {
        Livewire::withQueryParams(['inline' => false])
            ->visit($this->componentWithCustomStyles())
            ->click('@input')
            // Get the wrapper div around the dropdown list
            ->assertHasClass('div:has(> [dusk="dropdown"])', 'some-style')
            ->assertClassMissing('div:has(> [dusk="dropdown"])', 'absolute')
            ->assertClassMissing('div:has(> [dusk="dropdown"])', 'w-full')
        ;
    }

    /** @test */
    public function custom_result_focus_styles_are_used()
    {
        Livewire::visit($this->componentWithCustomStyles())
            ->click('@input')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@result-0', 'some-focus-style')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ;
    }
}
