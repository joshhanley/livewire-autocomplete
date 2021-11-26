@php
$inputProperty = $attributes->wire('model-text');
$resultsProperty = $attributes->wire('model-results');
$selectedProperty = $attributes->wire('model-id');
$focusAction = $attributes->wire('focus');

/** Remove all wire attributes that are assigned to local properties from the attribute bag */
$attributes = $attributes->whereDoesntStartWith('wire:');

$inputValue = $this->getPropertyValue($inputProperty->value);
$resultsValue = $this->getPropertyValue($resultsProperty->value);
$autoSelect = filter_var($getOption('auto-select'), FILTER_VALIDATE_BOOLEAN);
$allowNew = filter_var($getOption('allow-new'), FILTER_VALIDATE_BOOLEAN);
$loadOnceOnFocus = filter_var($getOption('load-once-on-focus'), FILTER_VALIDATE_BOOLEAN);
$inline = filter_var($getOption('inline'), FILTER_VALIDATE_BOOLEAN);
@endphp

<x-dynamic-component
    :component="$getComponent('outer-container')"
    x-data="autocomplete({
        name: '{{ $name }}',
        value: $wire.entangle('{{ $inputProperty->value }}'),
        debouncedValue: null,
        results: $wire.entangle('{{ $resultsProperty->value }}'),
        selected: $wire.entangle('{{ $selectedProperty->value }}'),
        focusAction: '{{ $focusAction->value ?? null }}',
        idAttribute: '{{ $getOption('id') }}',
        searchAttribute: '{{ $getOption('text') }}',
        autoSelect: {{ $autoSelect ? 'true' : 'false' }},
        allowNew: {{ $allowNew ? 'true' : 'false' }},
        loadOnceOnFocus: {{ $loadOnceOnFocus ? 'true' : 'false' }},
    })"
    x-init="init($dispatch)"
    x-on:click.away="away($dispatch)">
    <x-dynamic-component
        :component="$getComponent('input')"
        name="{{ $name }}"
        {{ $attributes }}
        x-model="debouncedValue"
        x-on:focus="inputFocus()"
        x-on:keydown.tab="tab($dispatch)"
        x-on:keydown.shift.window="shift(true)"
        {{-- Detect shift on window otherwise shift+tab from another field not recognised --}}
        x-on:keyup.shift.window.debounce.300ms="shift(false)"
        {{-- Detect shift on window otherwise shift+tab from another field not recognised --}}
        x-on:blur.window="shift(false)"
        {{-- Clear shift on window blur otherwise can't select --}}
        x-on:keydown.escape.prevent="escape($dispatch); event.target.blur()"
        x-on:keydown.enter.stop.prevent="enter($dispatch); event.target.blur()"
        x-on:keydown.arrow-up.prevent="focusPrevious()"
        x-on:keydown.arrow-down.prevent="focusNext()"
        x-on:keydown.home.prevent="focusFirst()"
        x-on:keydown.end.prevent="focusLast()"
        x-on:input.debounce.300ms="input($dispatch, $event.target.value)"
        x-spread="inputListeners()"
        x-bind="inputListeners()"
        dusk="autocomplete-input" />

    <x-dynamic-component
        :component="$getComponent('clear-button')"
        x-show="selected"
        x-on:click="clearItem($dispatch)"
        dusk="clear" />

    <x-dynamic-component
        :component="$getComponent('dropdown')"
        :class="$inline ? $getOption('inline-styles') : $getOption('overlay-styles')"
        x-show="shouldShow()"
        x-on:mouseleave="mouseLeave()"
        dusk="autocomplete-dropdown">
        <x-dynamic-component :component="$getComponent('loading')" dusk="autocomplete-loading" />

        <x-dynamic-component :component="$getComponent('results-container')">
            @if ($shouldShowPlaceholder($resultsValue, $inputValue))
                <x-dynamic-component :component="$getComponent('prompt')" wire:key="{{ $name }}-prompt" />
            @else
                @if ($hasResults($resultsValue) || $allowNew)
                    <x-dynamic-component :component="$getComponent('results-list')"
                        wire:key="{{ $name }}-results-list"
                        x-on:click.stop="selectItem($dispatch)">
                        @if ($allowNew && strlen($inputValue) > 0)
                            <x-dynamic-component
                                :component="$getComponent('add-new-row')"
                                :input-text="$inputValue"
                                wire:key='{{ $name }}-add-new'
                                x-on:mouseenter="focusIndex = 0"
                                x-bind:class="{ '{{ $getOption('result-focus-styles') }}' : focusIndex == 0}"
                                x-ref="add-new"
                                dusk="add-new" />
                        @endif

                        @if ($resultsValue)
                            @foreach ($resultsValue as $key => $result)
                                <x-dynamic-component
                                    :component="$getComponent('result-row')"
                                    :result="$result"
                                    text-attribute="{{ $getOption('text') }}"
                                    wire:key="{{ $name }}-result-{{ $key }}"
                                    x-on:mouseenter="focusIndex = {{ $allowNew && strlen($inputValue) > 0 ? $key + 1 : $key }}"
                                    x-bind:class="{ '{{ $getOption('result-focus-styles') }}' : focusIndex == {{ $allowNew && strlen($inputValue) > 0 ? $key + 1 : $key }} }"
                                    x-ref="result-{{ $key }}"
                                    dusk="result-{{ $key }}" />
                            @endforeach
                        @endif
                    </x-dynamic-component>
                @else
                    <x-dynamic-component :component="$getComponent('no-results')" wire:key='{{ $name }}-no-results' />
                @endif
            @endif
        </x-dynamic-component>
    </x-dynamic-component>
</x-dynamic-component>

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

                    this.$watch('focusIndex', () => this.scrollFocusedIntoView())

                    this.$watch('value', (newValue) => this.debounceInput(newValue))
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
                    if (this.focusAction) {
                        let target = this.parseOutMethodAndParams(this.focusAction)

                        this.$wire.call(target.method, ...target.params)
                    }

                    if (this.loadOnceOnFocus) this.focusAction = null

                    this.show()
                },

                parseOutMethodAndParams(rawMethod) {
                    // Parse any escaped html entities
                    let textArea = document.createElement('textarea');
                    textArea.innerHTML = rawMethod;
                    rawMethod = textArea.value;

                    let method = rawMethod
                    let params = []
                    const methodAndParamString = method.match(/(.*?)\((.*)\)/)

                    if (methodAndParamString) {
                        // This "$event" is for use inside the livewire event handler.
                        const $event = this.eventContext
                        method = methodAndParamString[1]
                        // use a function that returns it's arguments to parse and eval all params
                        params = eval(`(function () {
                            for (var l=arguments.length, p=new Array(l), k=0; k<l; k++) {
                                p[k] = arguments[k];
                            }
                            return [].concat(p);
                        })(${methodAndParamString[2]})`)
                    }

                    return {
                        method,
                        params
                    }
                },

                away($dispatch) {
                    if (!this.allowNew && this.autoSelect && !this.selected) this.resetValue($dispatch)

                    this.close()
                },

                escape($dispatch) {
                    if (!this.allowNew && this.autoSelect) this.resetValue($dispatch)

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
                    this.value = this.debouncedValue = null

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

                    this.resultsCount = this.results ? this.results.length : 0

                    if (this.allowNew && this.value !== null && this.value.length > 0) this.resultsCount++

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

                scrollFocusedIntoView() {
                    if (!this.showDropdown || this.focusIndex === null) return;

                    let scrollEl

                    if (this.allowNew && this.value !== null && this.value.length !== 0) {
                        if (this.focusIndex === 0) {
                            scrollEl = this.$refs['add-new']
                        } else {
                            scrollEl = this.$refs['result-' + (this.focusIndex - 1)]
                        }
                    } else {
                        scrollEl = this.$refs['result-' + this.focusIndex]
                    }

                    if (scrollEl === undefined)
                        return console.warn('"result-' + this.focusIndex + '" could not be found. Check you have @{{ $attributes }} in your result-row component.')

                    scrollEl.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                    })
                },

                mouseLeave() {
                    if (this.autoSelect) return

                    this.resetFocus()
                },

                input($dispatch, $inputValue) {
                    this.resetFocus()

                    this.value = $inputValue

                    $dispatch((this.name ?? 'autocomplete') + '-input', this.value)
                },

                selectItem($dispatch) {
                    if (this.hasFocus() && this.hasResults()) {
                        if (this.allowNew && this.value !== null && this.value.length !== 0) {
                            if (this.focusIndex !== 0)
                                this.setSelected($dispatch, this.results[this.focusIndex - 1])
                            else 
                                $dispatch((this.name ?? 'autocomplete') + '-add-new', this.value)
                        } else {
                            this.setSelected($dispatch, this.results[this.focusIndex])
                        }
                    } else {
                        if (!this.allowNew && this.autoSelect) {
                            this.resetValue($dispatch)
                        }
                    }

                    this.close()
                },

                setSelected($dispatch, selected) {
                    this.value = typeof selected === 'object' && selected.hasOwnProperty(this.searchAttribute) ? selected[this.searchAttribute] : selected
                    this.selected = typeof selected === 'object' && selected.hasOwnProperty(this.idAttribute) ? selected[this.idAttribute] : selected
                    $dispatch((this.name ?? 'autocomplete') + '-selected-object', selected)
                    $dispatch((this.name ?? 'autocomplete') + '-selected', this.selected)
                    $dispatch((this.name ?? 'autocomplete') + '-input', this.value)
                },

                debounceInput(newValue) {
                    if (this.debouncedValue != newValue) {
                        if (newValue === undefined) {
                            newValue = null;
                        }
                        this.debouncedValue = newValue;
                    }
                },

                clearItem($dispatch) {
                    this.selected = null
                    this.resetValue($dispatch)
                    $dispatch((this.name ?? 'autocomplete') + '-cleared')
                },

                dispatch(el, name, detail = {}) {
                    el.dispatchEvent(
                        new CustomEvent(name, {
                            detail,
                            bubbles: true,
                            // Allows events to pass the shadow DOM barrier.
                            composed: true,
                            cancelable: true,
                        })
                    )
                },

                inputListeners() {
                    return {
                        ['x-on:' + (this.name ?? 'autocomplete') + '-clear.window'](event) {
                            this.clearItem(this.dispatch.bind(this.dispatch, event.target))
                        },
                        ['x-on:' + (this.name ?? 'autocomplete') + '-set.window'](event) {
                            this.setSelected(this.dispatch.bind(this.dispatch, event.target), event.detail)
                        }
                    }
                }
            }
        }

    </script>
@endonce
