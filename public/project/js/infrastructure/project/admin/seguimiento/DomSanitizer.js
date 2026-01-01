export class DomSanitizer {
    static escape(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
}
//# sourceMappingURL=DomSanitizer.js.map