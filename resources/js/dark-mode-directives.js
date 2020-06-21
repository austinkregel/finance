/**
 * Due to the split nature of this app (for now) we have to do this stupid check to see if the store is set.
 * I don't remember why these pages are split out. They should really be joined together in a future update.
 * @param vNode
 */
const darkMode = (vNode) => vNode.context.hasOwnProperty('$store') ? vNode.context.$store.state.darkMode : vNode.context.$props.darkMode;

/**
 * For when the text is normally lighter, but not white.
 */
Vue.directive('dark-mode-light-text', {
    bind(el, binding, vNode) {
        el.classList.remove(!darkMode(vNode) ? 'text-gray-200' : 'text-gray-600')
        el.classList.add(darkMode(vNode) ? 'text-gray-200' : 'text-gray-600')
    }
})

Vue.directive('dark-mode-light-gray-text', {
    bind(el, binding, vNode) {
        el.classList.remove(!darkMode(vNode) ? 'text-gray-400' : 'text-gray-500')
        el.classList.add(darkMode(vNode) ? 'text-gray-400' : 'text-gray-500')
    }
});

/**
 * For when the text would normally be dark
 */
Vue.directive('dark-mode-dark-text', {
    bind(el, binding, vNode) {
        el.classList.remove(!darkMode(vNode) ? 'text-gray-200' : 'text-gray-800')
        el.classList.add(darkMode(vNode) ? 'text-gray-200' : 'text-gray-800')
    }
})

Vue.directive('dark-mode-white-background', {
    bind(el, binding, vNode) {
        el.classList.remove(!darkMode(vNode) ? 'bg-gray-700' : 'bg-white')
        el.classList.add(darkMode(vNode) ? 'bg-gray-700' : 'bg-white')
    }
})

Vue.directive('dark-mode-gray-background', {
    bind: function bind(el, binding, vNode) {
        el.classList.remove(!darkMode(vNode) ? 'bg-gray-700' : 'bg-gray-200');
        el.classList.add(darkMode(vNode) ? 'bg-gray-700' : 'bg-gray-200');
    }
});
Vue.directive('dark-mode-link', {
    bind: function bind(el, binding, vNode) {
        el.classList.remove(!darkMode(vNode) ? 'text-blue-400' : 'text-blue-600');
        el.classList.remove(!darkMode(vNode) ? 'hover:text-blue-300' : 'hover:text-blue-700')

        el.classList.add(darkMode(vNode) ? 'text-blue-400' : 'text-blue-600');
        el.classList.add(darkMode(vNode) ? 'hover:text-blue-300' : 'hover:text-blue-700')
    }
});

Vue.directive('dark-mode-input', {
    bind: function bind(el, binding, vNode) {
        el.classList.remove(!darkMode(vNode) ? 'bg-gray-800' : 'bg-white');
        el.classList.remove(!darkMode(vNode) ? 'text-white' : 'text-gray-800');

        el.classList.add(darkMode(vNode) ? 'bg-gray-800' : 'bg-white');
        el.classList.add(darkMode(vNode) ? 'text-white' : 'text-gray-800');
    }
});

Vue.directive('dark-mode-button', {
    bind: function bind(el, binding, vNode) {
        if (!el.classList.contains('border-2')) {
            el.classList.add('border-2')
        }

        el.classList.remove(!darkMode(vNode) ? 'border-blue-500' : 'border-blue-700');
        el.classList.remove(!darkMode(vNode) ? 'text-blue-100' : 'text-blue-700');
        el.classList.remove(!darkMode(vNode) ? 'hover:bg-blue-600' : 'hover:bg-blue-600');
        el.classList.remove(!darkMode(vNode) ? 'hover:text-white' : 'hover:text-white');

        el.classList.add(darkMode(vNode) ? 'border-blue-500' : 'border-blue-700');
        el.classList.add(darkMode(vNode) ? 'text-blue-100' : 'text-blue-700');
        el.classList.add(darkMode(vNode) ? 'hover:bg-blue-600' : 'hover:bg-blue-600');
        el.classList.add(darkMode(vNode) ? 'hover:text-white' : 'hover:text-white');
    }
});
