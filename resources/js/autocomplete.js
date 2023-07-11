document.addEventListener('alpine:init', () => {
    Alpine.data('autocomplete', (config) => ({
        open: false,
        key: config.key,
        value: null,
        valueProperty: null,
        focusedKey: null,
        items: null,
        root: null,
        shiftTab: false,

        init() {
            this.root = this.$el

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
            this.focusedKey = null
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

        focusKey(key) {
            if (this.keyFound(key)) this.focusedKey = key
        },

        focusPrevious() {
            let foundPosition = this.focusedKeyPosition()

            let previousFocusPosition = foundPosition - 1

            this.focusedKey = this.focusableItems[previousFocusPosition] ?? this.resetFocusedKey()
        },

        focusNext() {
            let foundPosition = this.focusedKeyPosition()

            let nextFocusPosition = foundPosition + 1

            this.focusedKey = this.focusableItems[nextFocusPosition] ?? this.focusedKey
        },

        focusFirst() {
            this.focusedKey = this.focusableItems[0] ?? null
        },

        focusLast() {
            this.focusedKey = this.focusableItems[this.focusableItems.length - 1] ?? null
        },

        escape($event) {
            this.close()

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
                // This adds support for int keys and string keys
                let focusedKey = typeof this.focusedKey === 'string' ? "'" + this.focusedKey + "'" : this.focusedKey

                let valueEl = this.root.querySelector(`[wire\\:autocomplete-key="${focusedKey}"]`)

                this.key = Alpine.evaluate(this.root, valueEl.getAttribute('wire:autocomplete-key'))
                this.value = Alpine.evaluate(this.root, valueEl.getAttribute('wire:autocomplete-value'))
            }

            this.hide()
        },

        hasSelectedItem() {
            return this.key !== null
        },

        clearSelectedItem() {
            this.key = null
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
            if (this.items !== null) return this.items

            this.items = [...this.root.querySelectorAll('[wire\\:autocomplete-key]:not([wire\\:autocomplete-disabled])')].map((el) =>
                Alpine.evaluate(this.root, el.getAttribute('wire:autocomplete-key'))
            )

            return this.items
        },
    }))
})
