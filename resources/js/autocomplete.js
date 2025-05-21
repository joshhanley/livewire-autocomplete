document.addEventListener('livewire:init', () => {
    Alpine.data('autocomplete', (config) => ({
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

            this.decoupledValue = this.value

            this.$watch('value', (newValue) => this.setDecoupledValue(newValue))
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

                this.$wire.$call(target.method, ...target.params)
            }

            if (this.loadOnceOnFocus) this.focusAction = null

            this.show()
        },

        parseOutMethodAndParams(rawMethod) {
            // Parse any escaped html entities
            let textArea = document.createElement('textarea')
            textArea.innerHTML = rawMethod
            rawMethod = textArea.value

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
                params,
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

        enter($dispatch, event) {
            this.selectItem($dispatch)

            if (this.selected) {
                event.preventDefault()
                event.target.blur()
            }
        },

        shift(isPressed) {
            this.shiftIsPressed = isPressed
        },

        close() {
            if (this.isHidden()) return

            this.hide()

            this.resetFocus()
        },

        resetFocus() {
            if (this.autoSelect) return (this.focusIndex = 0)

            this.focusIndex = null
        },

        resetValue($dispatch) {
            this.decoupledValue = null

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

            if (this.allowNew && this.decoupledValue !== null && this.decoupledValue.length > 0) this.resultsCount++

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

            if (this.focusIsAtStart()) return this.resetFocus()

            this.focusIndex--
        },

        focusNext() {
            if (this.hasNoResults()) return this.resetFocus()

            if (this.hasNoFocus()) return this.focusFirst()

            if (this.focusIsAtEnd()) return

            this.focusIndex++
        },

        scrollFocusedIntoView() {
            if (!this.showDropdown || this.focusIndex === null) return

            let scrollEl

            if (this.allowNew && this.decoupledValue !== null && this.decoupledValue.length !== 0) {
                if (this.focusIndex === 0) {
                    scrollEl = this.$refs['add-new']
                } else {
                    scrollEl = this.$refs['result-' + (this.focusIndex - 1)]
                }
            } else {
                scrollEl = this.$refs['result-' + this.focusIndex]
            }

            if (scrollEl === undefined) return console.warn('"result-' + this.focusIndex + '" could not be found. Check you have @{{ $attributes }} in your result-row component.')

            scrollEl.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
            })
        },

        mouseLeave() {
            if (this.autoSelect) return

            this.resetFocus()
        },

        input($dispatch) {
            this.resetFocus()

            this.value = this.decoupledValue

            $dispatch((this.name ?? 'autocomplete') + '-input', this.decoupledValue)
        },

        selectItem($dispatch) {
            if (this.hasFocus() && this.hasResults()) {
                if (this.allowNew && this.decoupledValue !== null && this.decoupledValue.length !== 0) {
                    if (this.focusIndex !== 0) this.setSelected($dispatch, this.results[this.focusIndex - 1])
                    else $dispatch((this.name ?? 'autocomplete') + '-add-new', this.decoupledValue)
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
            const newValue = typeof selected === 'object' && selected.hasOwnProperty(this.searchAttribute) ? selected[this.searchAttribute] : selected
            if (newValue === this.value) {
                return
            }

            this.decoupledValue = null
            this.value = newValue
            this.selected = typeof selected === 'object' && selected.hasOwnProperty(this.idAttribute) ? selected[this.idAttribute] : selected
            $dispatch((this.name ?? 'autocomplete') + '-selected-object', selected)
            $dispatch((this.name ?? 'autocomplete') + '-selected', this.selected)
            $dispatch((this.name ?? 'autocomplete') + '-input', this.value)
        },

        setDecoupledValue(newValue) {
            if (this.$refs.input === document.activeElement) {
                return
            }

            this.decoupledValue = newValue
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
                },
            }
        },
    }))
})
