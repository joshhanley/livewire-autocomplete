document.addEventListener('alpine:init', () => {
    Alpine.data('autocomplete', (config) => ({
        open: false,
        key: config.key,
        value: null,
        valueProperty: null,
        focusedKey: null,
        items: null,
        root: null,

        init() {
            this.root = this.$el

            this.$nextTick(() => {
                this.$wire.__instance.watch(this.valueProperty, () => {
                    this.value = this.$wire.get(this.valueProperty)
                    this.$nextTick(() => this.itemsChanged())
                })
            })
        },

        inputFocus() {
            this.show()
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

        focusPrevious() {
            let foundPosition = this.focusedKeyPosition()

            let previousFocusPosition = foundPosition - 1

            this.focusedKey = this.focusableItems[previousFocusPosition] ?? this.focusedKey
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

        enter() {
            if (this.focusedKeyFound()) {
                let valueEl = this.root.querySelector(`[wire\\:autocomplete-key="${this.focusedKey}"]`)

                this.key = Alpine.evaluate(this.root, valueEl.getAttribute('wire:autocomplete-key'))
                this.value = Alpine.evaluate(this.root, valueEl.getAttribute('wire:autocomplete-value'))

                this.hide()
            }
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

            this.items = [...this.root.querySelectorAll('[wire\\:autocomplete-key]:not([wire\\:autocomplete-disabled])')].map((el) => el.getAttribute('wire:autocomplete-key'))

            return this.items
        },
    }))
})
