const toolbarOptions = [
    [{'size' : []}],
    ['bold', 'italic', 'underline', 'strike'],
    ['link', 'code-block'],
    [{'color' : []}, {'background' : []}],
    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    [{ 'script': 'sub'}, { 'script': 'super' }],
    ['clean']
]

const quill = new Quill('#editor', {
    modules: {
        syntax: true,
        toolbar: toolbarOptions
    },
    theme: 'snow',
    placeholder: "What's on your mind?..."
});

const form = document.querySelector("#postForm");
form.addEventListener('formdata', (event) => {
    event.formData.append('text', quill.getSemanticHTML());
})

document.querySelectorAll('pre').forEach((el) => {
    hljs.highlightElement(el);
});