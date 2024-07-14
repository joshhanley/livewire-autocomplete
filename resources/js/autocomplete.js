document.addEventListener('alpine:init', () => {
    Alpine.data('autocomplete', (config) => ({
        open: false,
        key: null,
        id: config.id,
        value: null,
        valueProperty: null,
        focusedIndex: null,
        focusedIndexKeyValue: -1,
        items: null,
        root: null,
        shiftTab: false,
        autoSelect: config.autoSelect,

        init() {
            this.root = this.$el

            this.resetFocusedIndex()

            this.$watch('focusedIndex', () => {
                this.scrollFocusedIntoView()

                console.log('watchFocusedIndex', this.focusedIndex)
                this.resetFocusedIndexKey()
                console.log('watchFocusedIndexReset', this.focusedIndexKeyValue)
            })
            // this.$watch('focusedIndex', () => {
            //     console.log('focusedIndexChanged', this.focusedIndex)
            //     this.resetFocusedIndexKey()
            //     console.log('focusedIndexKeyValue', this.focusedIndexKeyValue)
            // })

            this.$watch('focusedIndexKeyValue', () => {
                console.log('focusedIndexKeyValueChanged', this.focusedIndexKeyValue)
            })
            // this.$watch('focusedIndex', () => console.log('focusedIndexChanged'))

            // this.$nextTick(() => {
            //     this.$wire.watch(this.valueProperty, () => {
            //         this.value = this.$wire.get(this.valueProperty)
            //         this.$nextTick(() => this.itemsChanged())
            //     })
            // })

            Livewire.hook('commit', ({component, succeed}) => {
                console.log('commit', component, this.$wire.__instance)
                if (component.id === this.$wire.id) {
                    console.log('componentSame')

                    succeed(() => {
                        console.log('succeed')
                        this.$nextTick(() => this.itemsChanged())
                    })
                }
            })
        },

        clear() {
            this.clearSelectedItem()
            this.clearInput()
        },

        inputFocus() {
            this.show()
        },

        clearInput() {
            this.value = null
        },

        close() {
            this.hide()
        },

        show() {
            this.open = true
        },

        hide() {
            this.open = false
        },

        toggle() {
            this.open = !this.open
        },

        hasFocusedIndex() {
            return this.focusedIndex !== null
        },

        notHaveFocusedIndex() {
            return !this.hasFocusedIndex()
        },

        focusedIndexKey() {
            console.log('focusedIndexKeyMethod', this.focusedIndexKeyValue)
            // Memoise the focused index key
            if (this.focusedIndexKeyValue !== -1) return this.focusedIndexKeyValue

            console.log('calcFocusedIndexKey', this.focusedIndex, this.focusableItems, this.focusableItems[this.focusedIndex] ?? null)
            this.focusedIndexKeyValue = this.focusableItems[this.focusedIndex] ?? null

            console.log(this.focusedIndexKeyValue)
            return this.focusedIndexKeyValue
        },

        resetFocusedIndexKey() {
            console.log('resetFocusedIndexKEY')
            this.focusedIndexKeyValue = -1
        },

        focusedIndexFound() {
            console.log(this.focusedIndexKey())
            this.resetFocusedIndexKey()

            return this.focusedIndexKey() !== null
        },

        focusedIndexNotFound() {
            return !this.focusedIndexFound()
        },

        resetFocusedIndex() {
            console.log('resetFocusedIndex')
            // this.resetFocusedIndexKey()
            if (this.autoSelect === true) {
                this.resetFocusedIndexKey()
                if (this.notHaveFocusedIndex()) {
                    // this.resetFocusedIndexKey()
                    console.log('focusfirst')
                    console.log(this.focusedIndex)
                    this.focusFirst()
                    console.log(this.focusedIndex)
                }

                return
            }

            console.log('reset')
            console.log(this.focusedIndex)
            this.resetFocusedIndexKey()
            this.focusedIndex = null
            console.log(this.focusedIndex)
        },

        focusedElement() {
            let focusedKey = this.focusedIndexKey()

            // This adds support for int keys and string keys
            focusedKey = typeof focusedKey === 'string' ? "'" + focusedKey + "'" : focusedKey

            return this.root.querySelector(`[wire\\:autocomplete-key="${focusedKey}"]`)
        },

        scrollFocusedIntoView() {
            let el = this.focusedElement()

            if (!el) return

            el.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
            })
        },

        focusedIndexIsNewItemRow() {
            return this.focusedIndexKey() === '_x_autocomplete_new'
        },

        focusedIndexIsNotNewItemRow() {
            return !this.focusedIndexIsNewItemRow()
        },

        indexKey(index) {
            return this.focusableItems[index] ?? null
        },

        keyFound(key) {
            return this.focusableItems.includes(key)
        },

        indexFound(index) {
            return this.indexKey(index) !== null
        },

        indexNotFound(index) {
            return !this.indexFound()
        },

        firstIndex() {
            console.log('length', this.focusableItems, this.focusableItems?.length)
            return this.focusableItems.length ? 0 : null
        },

        lastIndex() {
            return this.focusableItems.length ? this.focusableItems.length - 1 : null
        },

        focusKey(key) {
            if (this.keyFound(key)) this.focusedIndex = this.focusableItems.indexOf(key)
        },

        focusIndex(index) {
            if (this.focusableItems.length - 1 >= index) this.focusedIndex = index
        },

        focusPrevious() {
            let previousFocusIndex = this.focusedIndex - 1

            if (this.focusableItems[previousFocusIndex] !== undefined) {
                this.focusedIndex = previousFocusIndex

                return
            }

            this.resetFocusedIndex()
        },

        focusNext() {
            let nextFocusIndex = this.focusedIndex === null ? 0 : this.focusedIndex + 1

            if (this.focusableItems[nextFocusIndex] !== undefined) {
                this.focusedIndex = nextFocusIndex
            }
        },

        focusFirst() {
            console.log('focusFirst')
            this.focusedIndex = this.firstIndex()
        },

        focusLast() {
            this.focusedIndex = this.lastIndex()
        },

        outside() {
            this.close()

            if (this.autoSelect && this.notHaveSelectedItem() && this.notHaveNewItem()) {
                this.clear()
            }
        },

        escape($event) {
            this.close()

            if (this.autoSelect && this.notHaveSelectedItem() && this.notHaveNewItem()) {
                this.clear()
            }

            $event.target.blur()
        },

        tab() {
            if (this.shiftTab == true) return (this.shiftTab = false)

            this.selectItem()
        },

        shiftTab() {
            this.shiftTab = true
        },

        enter($event) {
            this.selectItem()

            if (this.hasSelectedItem()) {
                $event.preventDefault()

                $event.target.blur()
            }
        },

        selectItem() {
            console.log('selectItem', this.focusedIndexFound(), this.focusedIndexIsNotNewItemRow())
            // If key is set to new, then do not process the key and value
            if (this.focusedIndexFound() && this.focusedIndexIsNotNewItemRow()) {
                let valueEl = this.focusedElement()

                let id = valueEl.getAttribute('wire:autocomplete-id')

                this.key = Alpine.evaluate(this.root, valueEl.getAttribute('wire:autocomplete-key'))
                this.id = id ? Alpine.evaluate(this.root, id) : this.key
                this.value = Alpine.evaluate(this.root, valueEl.getAttribute('wire:autocomplete-value'))
            }

            if (this.focusedIndexNotFound() && this.autoSelect) {
                this.clear()
            }

            this.hide()
        },

        hasSelectedItem() {
            return this.id !== null
        },

        notHaveSelectedItem() {
            return !this.hasSelectedItem()
        },

        clearSelectedItem() {
            this.key = null
            this.id = null
        },

        hasNewItem() {
            return !!this.focusableItems?.find((item) => item == '_x_autocomplete_new')
        },

        notHaveNewItem() {
            return !this.hasNewItem()
        },

        itemsChanged() {
            this.clearItems()

            // if (this.focusedIndexNotFound()) {
            this.resetFocusedIndex()
            this.$refs['autocomplete-list'].scroll({
                top: 0,
                // behavior: 'smooth',
            })
            // }
        },

        clearItems() {
            this.items = null
        },

        get focusableItems() {
            // Disable memoisation for now as it is causing an inconsistency.
            // if (this.items !== null) return this.items

            this.items = [...this.root.querySelectorAll('[wire\\:autocomplete-key]:not([wire\\:autocomplete-disabled])')]
                .map((el) => Alpine.evaluate(this.root, el.getAttribute('wire:autocomplete-key')))
                .filter((item) => item != '_x_autocomplete_empty')

            return this.items
        },
    }))
})
