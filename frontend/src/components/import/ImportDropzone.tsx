import {useCallback} from 'react';
import {useDropzone} from 'react-dropzone';

const ImportDropzone = (props: {
    // eslint-disable-next-line no-unused-vars
    onDrop?: (data: ArrayBuffer) => void,
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    children?: any
}) => {
  const onDrop = useCallback(<T extends File>(acceptedFiles: T[]) => {
    acceptedFiles.forEach((file) => {
      const reader = new FileReader();

      reader.onabort = () => console.error('file reading was aborted');
      reader.onerror = () => console.error('file reading has failed');
      reader.onload = () => {
        if (props.onDrop !== undefined) { setTimeout(props.onDrop, 0, reader.result); }
      };
      reader.readAsArrayBuffer(file);
    });
  }, [props.onDrop]);

  const {getRootProps, getInputProps} = useDropzone({onDrop});

  return (
        <div {...getRootProps()}>
            <input {...getInputProps()} />
            {props.children}
        </div>
  );
};

export default ImportDropzone;
