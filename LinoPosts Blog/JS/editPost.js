const toolbarOptions = [
    [{'size' : []}],
    ['bold', 'italic', 'underline', 'strike'],
    ['link', 'code-block'],
    [{'color' : []}, {'background' : []}],
    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    [{ 'script': 'sub'}, { 'script': 'super' }],
    ['clean']
]

if(document.querySelector("#text")){
    const editQuill = new Quill('#text', {
        modules: {
            syntax: true,
            toolbar: toolbarOptions
        },
        theme: 'snow',
        placeholder: "What's on your mind?..."
    });

    const form = document.querySelector("#editForm");
    form.addEventListener('formdata', (event) => {
        event.formData.append('text', editQuill.getSemanticHTML());
    })

    const htmlContent = document.querySelector("#htmlContent").value;
    editQuill.clipboard.dangerouslyPasteHTML(htmlContent);
}