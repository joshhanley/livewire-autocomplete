@php
$inputProperty = $attributes->wire('model-text');
$resultsProperty = $attributes->wire('model-results');
$selectedProperty = $attributes->wire('model-id');
$focusAction = $attributes->wire('focus');

/** Remove all wire attributes that are assigned to local properties from the attribute bag */
$attributes = $attributes->whereDoesntStartWith('wire:');
@endphp

<div
    x-data="autocomplete({
        name: {{ json_encode($name) }},
        value: {!! $inputProperty->value ? " \$wire.entangle('" . $inputProperty . "')" : 'null' !!},
        results: @entangle($resultsProperty),
        selected: {!! $selectedProperty->value ? "\$wire.entangle('" . $selectedProperty . "')" : 'null' !!},
        focusAction: {!! "'" . $focusAction->value . "'" ?? 'null' !!},
        idAttribute: '{{ $getOption('id') }}',
        searchAttribute: '{{ $getOption('text') }}',
        autoSelect: {{ $getOption('auto_select') ? 'true' : 'false' }},
        allowNew: {{ $getOption('allow_new') ? 'true' : 'false' }},
        loadOnceOnFocus: {{ $getOption('load_once_on_focus') ? 'true' : 'false' }},
        })"
    x-init="init($dispatch)"
    x-on:click.away="away($dispatch)">
    <div class="relative">
        <input
            x-model.debounce.300ms="value"
            x-on:focus="inputFocus()"
            x-on:keydown.tab="tab($dispatch)"
            x-on:keydown.shift.window="shift(true)"
            {{-- Detect shift on window otherwise shift+tab from another field not recognised --}}
            x-on:keyup.shift.window="shift(false)"
            {{-- Detect shift on window otherwise shift+tab from another field not recognised --}}
            x-on:blur.window="shift(false)"
            {{-- Clear shift on window blur otherwise can't select --}}
            x-on:keydown.escape.prevent="escape($dispatch); event.target.blur()"
            x-on:keydown.enter.stop.prevent="enter($dispatch); event.target.blur()"
            x-on:keydown.arrow-up.prevent="focusPrevious()"
            x-on:keydown.arrow-down.prevent="focusNext()"
            x-on:keydown.home.prevent="focusFirst()"
            x-on:keydown.end.prevent="focusLast()"
            x-on:input.debounce.300ms="input($dispatch)"
            class="w-full pl-4 py-2 rounded border border-cool-gray-200 shadow-inner leading-5 text-cool-gray-900 placeholder-cool-gray-400"
            x-bind:class="[selected ? 'pr-9' : 'pr-4']"
            type="text"
            dusk="autocomplete-input"
            x-bind:disabled="selected"
            x-spread="inputListeners()" />

        <div x-on:click="clearItem($dispatch)" class="absolute right-0 inset-y-0 flex items-center">
            <button x-show="selected" type="button" class="group focus:outline-none" dusk="clear" x-cloak>
                <div class="mr-3">
                    <svg class="h-5 w-5 border-2 border-gray-300 rounded group-focus:border-blue-400 bg-white text-gray-700 fill-current transition-transform ease-in-out duration-100 transform hover:scale-105 hover:text-black"
                        viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z"></path>
                    </svg>
                </div>
            </button>
        </div>
    </div>

    <div
        x-show="shouldShow()"
        class="{{ $getOption('inline') ? $getOption('inline_styles') : $getOption('overlay_styles') }} mt-0.5 px-2 w-full"
        x-on:mouseleave="mouseLeave()"
        x-transition:enter="transition ease-out duration-100 origin-top"
        x-transition:enter-start="transform opacity-0 scale-y-90"
        x-transition:enter-end="transform opacity-100 scale-y-100"
        x-transition:leave="transition ease-in duration-75 origin-top"
        x-transition:leave-start="transform opacity-100 scale-y-100"
        x-transition:leave-end="transform opacity-0 scale-y-90"
        dusk="autocomplete-dropdown"
        x-cloak>
        <div
            class="relative max-h-56 overflow-y-auto rounded-md border border-gray-300 bg-white shadow">
            <div wire:loading.delay.class.remove="hidden"
                class="hidden absolute inset-0 flex items-center justify-center" dusk="autocomplete-loading">
                <div class="absolute inset-0 bg-gray-500 opacity-25"></div>
                <svg class="animate-spin h-4 w-4 text-cool-gray-700 stroke-current" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            @if ($shouldShowPlaceholder($this->getPropertyValue($resultsProperty), $this->getPropertyValue($inputProperty->value)))
                <div wire:key="{{ $name }}-placeholder">
                    <div class="px-3 py-2">
                        {{ $resultsPlaceholder }}
                    </div>
                </div>
            @else
                @if ($hasResults($this->getPropertyValue($resultsProperty)) || $getOption('allow_new'))
                    <div wire:key="{{ $name }}-results" x-on:click.stop="selectItem($dispatch)"
                        class="divide-y divide-transparent cursor-pointer">
                        @if ($getOption('allow_new') && strlen($this->getPropertyValue($inputProperty)) > 0)
                            <div
                                wire:key='add-new'
                                x-on:mouseenter="focusIndex = 0"
                                :class="{ '{{ $getOption('result_focus_styles') }}' : focusIndex == 0}"
                                dusk="add-new">
                                <div class="px-3 py-2">
                                    Add new "{{ $this->getPropertyValue($inputProperty) }}"
                                </div>
                            </div>
                        @endif

                        @if ($this->getPropertyValue($resultsProperty))
                            @foreach ($this->getPropertyValue($resultsProperty) as $key => $result)
                                <div
                                    wire:key="result-{{ $key }}"
                                    x-on:mouseenter="focusIndex = {{ $getOption('allow_new') && strlen($this->getPropertyValue($inputProperty)) > 0 ? $key + 1 : $key }}"
                                    :class="{ '{{ $getOption('result_focus_styles') }}' : focusIndex == {{ $getOption('allow_new') && strlen($this->getPropertyValue($inputProperty)) > 0 ? $key + 1 : $key }}}"
                                    dusk="result-{{ $key }}">
                                    @if ($resultComponent)
                                        <x-dynamic-component class="px-3 py-2" :component="$resultComponent" :model="$result" />
                                    @else
                                        <div class="px-3 py-2">
                                            {{ $result[$getOption('text')] ?? $result }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                @else
                    <div wire:key="{{ $name }}-no-results">
                        <div class="px-3 py-2">
                            {{ $noResults }}
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@once
    <script>
        function autocomplete(config) {
            return {
                showDropdown: false,
                ...config,
                focusIndex: null,
                resultsCount: null,
                shiftIsPressed: false,
                selectOnTab: true,

                init($dispatch) {
                    this.$watch('results', () => this.clearResultsCount())

                    this.resetFocus()
                },

                show() {
                    this.showDropdown = true
                },

                hide() {
                    this.showDropdown = false
                },

                isShown() {
                    return this.showDropdown
                },

                isHidden() {
                    return !this.isShown()
                },

                shouldShow() {
                    return this.showDropdown
                },

                inputFocus() {
                    if (this.focusAction && !this.value) this.$wire.call(this.focusAction)

                    if (this.loadOnceOnFocus) this.focusAction = null

                    this.show()
                },

                away($dispatch) {
                    if (this.autoSelect && !this.selected) this.resetValue($dispatch)

                    this.close()
                },

                escape($dispatch) {
                    if (this.autoSelect) this.resetValue($dispatch)

                    this.hide()
                },

                tab($dispatch) {
                    if (this.shiftIsPressed) return this.close()

                    if (this.selectOnTab) return this.selectItem($dispatch)

                    return this.close()
                },

                enter($dispatch) {
                    this.selectItem($dispatch)
                },

                shift(isPressed) {
                    this.shiftIsPressed = isPressed
                },

                close() {
                    if (this.isHidden()) return

                    this.hide()

                    this.resetFocus();
                },

                resetFocus() {
                    if (this.autoSelect) return this.focusIndex = 0

                    this.focusIndex = null
                },

                resetValue($dispatch) {
                    this.value = null

                    this.input($dispatch)
                },

                hasResults() {
                    return this.totalResults() > 0
                },

                hasNoResults() {
                    return !this.hasResults()
                },

                clearResultsCount() {
                    this.resultsCount = null
                },

                totalResults() {
                    if (this.resultsCount) return this.resultsCount //Use memoised count

                    this.resultsCount = this.results.length

                    if (this.allowNew && this.value.length > 0) this.resultsCount++

                    return this.resultsCount
                },

                hasFocus() {
                    return this.focusIndex !== null
                },

                hasNoFocus() {
                    return !this.hasFocus()
                },

                focusIsAtStart() {
                    return this.focusIndex == 0
                },

                focusIsAtEnd() {
                    return this.focusIndex >= this.totalResults() - 1
                },

                focusFirst() {
                    this.focusIndex = 0
                },

                focusLast() {
                    this.focusIndex = this.totalResults() - 1
                },

                focusPrevious() {
                    if (this.hasNoResults()) return this.resetFocus()

                    if (this.hasNoFocus()) return

                    if (this.focusIsAtStart()) return this.resetFocus();

                    this.focusIndex--
                },

                focusNext() {
                    if (this.hasNoResults()) return this.resetFocus()

                    if (this.hasNoFocus()) return this.focusFirst()

                    if (this.focusIsAtEnd()) return

                    this.focusIndex++
                },

                mouseLeave() {
                    if (this.autoSelect) return

                    this.resetFocus()
                },

                input($dispatch) {
                    this.resetFocus()

                    $dispatch((this.name ?? 'autocomplete') + '-input', this.value)
                },

                selectItem($dispatch) {
                    if (this.hasFocus() && this.hasResults()) {
                        if (!this.allowNew || this.value.length === 0 || this.focusIndex !== 0)
                            this.setSelected($dispatch, this.results[this.focusIndex])
                    } else {
                        if (this.autoSelect) {
                            this.resetValue($dispatch)
                        }
                    }

                    this.close()
                },

                setSelected($dispatch, selected) {
                    this.value = typeof selected === 'object' && selected.hasOwnProperty(this.searchAttribute) ? selected[this.searchAttribute] : selected
                    this.selected = typeof selected === 'object' && selected.hasOwnProperty(this.idAttribute) ? selected[this.idAttribute] : selected
                    $dispatch((this.name ?? 'autocomplete') + '-selected', this.selected)
                    $dispatch((this.name ?? 'autocomplete') + '-input', this.value)
                },

                clearItem($dispatch) {
                    this.selected = null
                    this.resetValue($dispatch)
                    $dispatch((this.name ?? 'autocomplete') + '-cleared')
                },

                inputListeners() {
                    return {
                        ['x-on:' + (this.name ?? 'autocomplete') + '-clear.window'](event) {
                            this.clearItem(this.$el.__x.getDispatchFunction(event.target))
                        },
                        ['x-on:' + (this.name ?? 'autocomplete') + '-set.window'](event) {
                            this.setSelected(this.$el.__x.getDispatchFunction(event.target), event.detail)
                        }
                    }
                }
            }
        }

    </script>
@endonce
