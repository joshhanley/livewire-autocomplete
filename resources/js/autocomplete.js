document.addEventListener('alpine:init', () => {
    Alpine.data('autocomplete', (config) => ({
        open: false,
        key: null,
        id: config.id,
        value: null,
        valueProperty: null,
        focusedKey: null,
        items: null,
        root: null,
        shiftTab: false,
        autoSelect: config.autoSelect,

        init() {
            this.root = this.$el

            this.resetFocusedKey()

            this.$watch('focusedKey', () => this.scrollFocusedIntoView())

            this.$nextTick(() => {
                this.$wire.watch(this.valueProperty, () => {
                    this.value = this.$wire.get(this.valueProperty)
                    this.$nextTick(() => this.itemsChanged())
                })
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

        hasFocusedKey() {
            return this.focusedKey !== null
        },

        notHaveFocusedKey() {
            return !this.hasFocusedKey()
        },

        focusedKeyPosition() {
            return this.focusableItems.indexOf(this.focusedKey)
        },

        focusedKeyFound() {
            return this.focusedKeyPosition() >= 0
        },

        focusedKeyNotFound() {
            return !this.focusedKeyFound()
        },

        resetFocusedKey() {
            if (this.autoSelect === true) {
                if (this.notHaveFocusedKey()) {
                    this.focusFirst()
                }

                return
            }

            this.focusedKey = null
        },

        focusedElement() {
            // This adds support for int keys and string keys
            let focusedKey = typeof this.focusedKey === 'string' ? "'" + this.focusedKey + "'" : this.focusedKey

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

        focusedKeyIsNewItemRow() {
            return this.focusedKey === '_x_autocomplete_new'
        },

        focusedKeyIsNotNewItemRow() {
            return !this.focusedKeyIsNewItemRow()
        },

        keyPosition(key) {
            return this.focusableItems.indexOf(key)
        },

        keyFound(key) {
            return this.keyPosition(key) >= 0
        },

        keyNotFound(key) {
            return !this.keyFound()
        },

        firstKey() {
            return this.focusableItems[0] ?? null
        },

        lastKey() {
            return this.focusableItems[this.focusableItems.length - 1] ?? null
        },

        focusKey(key) {
            if (this.keyFound(key)) this.focusedKey = key
        },

        focusPrevious() {
            let foundPosition = this.focusedKeyPosition()

            let previousFocusPosition = foundPosition - 1

            if (this.focusableItems[previousFocusPosition]) {
                this.focusedKey = this.focusableItems[previousFocusPosition]

                return
            }

            this.resetFocusedKey()
        },

        focusNext() {
            let foundPosition = this.focusedKeyPosition()

            let nextFocusPosition = foundPosition + 1

            if (this.focusableItems[nextFocusPosition]) {
                this.focusedKey = this.focusableItems[nextFocusPosition]
            }
        },

        focusFirst() {
            this.focusedKey = this.firstKey()
        },

        focusLast() {
            this.focusedKey = this.lastKey()
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
            // If key is set to new, then do not process the key and value
            if (this.focusedKeyFound() && this.focusedKeyIsNotNewItemRow()) {
                let valueEl = this.focusedElement()

                let id = valueEl.getAttribute('wire:autocomplete-id')

                this.key = Alpine.evaluate(this.root, valueEl.getAttribute('wire:autocomplete-key'))
                this.id = id ? Alpine.evaluate(this.root, id) : this.key
                this.value = Alpine.evaluate(this.root, valueEl.getAttribute('wire:autocomplete-value'))
            }

            if (this.focusedKeyNotFound() && this.autoSelect) {
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
            return !!this.items?.find((item) => item == '_x_autocomplete_new')
        },

        notHaveNewItem() {
            return !this.hasNewItem()
        },

        itemsChanged() {
            this.clearItems()

            // if (this.focusedKeyNotFound()) {
            this.resetFocusedKey()
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
