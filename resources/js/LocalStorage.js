const initLocalStorage = (property, defaultValue) => {
    if (!localStorage.hasOwnProperty(property)) {
        setLocalStorage(property, defaultValue);
    }
}
const setLocalStorage = (property, value) => {
    localStorage.setItem(property,  JSON.stringify(value));
}
const findLocalStorage = (property, defaultValue) => {
    if (!localStorage.hasOwnProperty(property)) {
        return defaultValue
    }

    return JSON.parse(localStorage.getItem(property));
}

export {
    initLocalStorage,
    setLocalStorage,
    findLocalStorage
}
