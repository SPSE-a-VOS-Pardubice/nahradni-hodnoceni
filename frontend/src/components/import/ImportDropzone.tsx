import { useCallback } from "react"
import { useDropzone } from "react-dropzone"

const ImportDropzone = (props: {
    onDrop?: (data: ArrayBuffer) => void,
    children?: any
}) => {
    const onDrop = useCallback(<T extends File>(acceptedFiles: T[]) => {
        acceptedFiles.forEach((file) => {
            const reader = new FileReader()

            reader.onabort = () => console.error('file reading was aborted')
            reader.onerror = () => console.error('file reading has failed')
            reader.onload = () => {
                props.onDrop && setTimeout(props.onDrop, 0, reader.result);
            }
            reader.readAsArrayBuffer(file)
        })
    }, [])
    const { getRootProps, getInputProps } = useDropzone({ onDrop })

    return (
        <div {...getRootProps()}>
            <input {...getInputProps()} />
            {props.children}
        </div>
    )
}

export default ImportDropzone;
