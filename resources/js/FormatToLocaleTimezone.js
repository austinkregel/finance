export default (option, dayjsClass, dayjsFactory) => {
    // dayjs(new Date().toLocaleString("en-US", { timeZone: "America/New_York" })).format('LLL')
    dayjsClass.prototype.formatToLocaleTimezone = function() {
        let date = this.toDate();

        return dayjsFactory(date.toLocaleString("en-US", {
            timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone
        })).format("LLL");
    }
}
