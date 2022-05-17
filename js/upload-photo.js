(() => {
    const HIDDEN = 'visually-hidden';

    const fileInputNode = document.querySelector('#photo-file');
    const uploadButtonNode = document.querySelector('#upload-button');
    const uploadFileNameNode = document.querySelector('#upload-file-name');
    const uploadRemoveButtonNode = document.querySelector('#upload-remove-button');
    const uploadPreviewImageNode = document.querySelector('#upload-preview-image');
    const uploadPreviewContainerNode = document.querySelector('#upload-preview-container');

    let currentUploadPreviewUrl = null;

    if (
        !fileInputNode ||
        !uploadButtonNode ||
        !uploadFileNameNode ||
        !uploadRemoveButtonNode ||
        !uploadPreviewContainerNode
    ) {
        return;
    }

    const onUploadButtonNodeClick = () => {
        fileInputNode.click();
    };


    const onUploadRemoveButtonNodeClick = () => {
        fileInputNode.value = '';

        uploadPreviewImageNode.src = '';
        uploadFileNameNode.textContent = '';
        uploadPreviewContainerNode.classList.add(HIDDEN);

        URL.revokeObjectURL(currentUploadPreviewUrl);
        currentUploadPreviewUrl = null;
    };

    const onFileInputNodeChange = (evt) => {
        const { files } = evt.target;
        const [file] = files;

        if (!file) {
            uploadPreviewContainerNode.classList.add(HIDDEN);
            return;
        }

        if (currentUploadPreviewUrl) {
            URL.revokeObjectURL(currentUploadPreviewUrl);
        }

        currentUploadPreviewUrl = URL.createObjectURL(file);

        uploadPreviewImageNode.src = currentUploadPreviewUrl;
        uploadFileNameNode.textContent = file.name;
        uploadPreviewContainerNode.classList.remove(HIDDEN);
    }

    uploadPreviewImageNode.src = '';
    uploadFileNameNode.textContent = '';
    fileInputNode.classList.add(HIDDEN);
    uploadPreviewContainerNode.classList.add(HIDDEN);

    fileInputNode.addEventListener('change', onFileInputNodeChange);
    uploadButtonNode.addEventListener('click', onUploadButtonNodeClick);
    uploadRemoveButtonNode.addEventListener('click', onUploadRemoveButtonNodeClick);
})();
