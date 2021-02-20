import compromise from 'compromise';

compromise.extend((Doc, world, _nlp, Phrase, Term, Pool)=>{
    Doc.prototype.isError = function() {
        console.log(/[error]/.test(this.text()), this.text())
        return /[error]/.test(this.text())
    }
})

export default compromise;
