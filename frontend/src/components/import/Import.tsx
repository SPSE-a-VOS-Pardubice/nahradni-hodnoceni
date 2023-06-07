import { FormattedMessage } from "react-intl";
import Popup from "../PopupComponent";
import ImportDropzone from "./ImportDropzone";
import { useEffect, useState } from "react";
import Subject from "../../models/data/Subject";
import Teacher from "../../models/data/Teacher";
import { uploadCsvFile, uploadData } from "../../ApiClient";
import FailedUploadResponse from "../../models/FailedUploadResponse";


const Import = (props: {
    open: boolean,
    onFinish: () => void
}) => {

    const [file, setFile] = useState<null | ArrayBuffer>(null);

    const [missingImport, setMissingImport] = useState<null | "" | FailedUploadResponse>(null);

    function handleUpload(data: ArrayBuffer) {
        setFile(data);
    }


    
    useEffect(() => {
        if (file === null) {
            setMissingImport(null);
            return;
        }

        (async () => {
            setMissingImport(await uploadCsvFile(file));
        })();
    }, [file]);



    if (!props.open || missingImport === "")
        return (<></>);

    if (missingImport != null && missingImport.missingSubjects.length > 0) {
        const subjectAbbreviation = missingImport.missingSubjects[0];

        return (
            <Popup open={true} options={[
                {
                    text: "OK",
                    onClick: async () => {
                        await uploadData(
                            {
                                name: (document.getElementById("subject_name")! as HTMLInputElement).value,
                                abbreviation: subjectAbbreviation
                            } as Subject,
                            "subject"
                        )

                        const mi = Object.assign({}, missingImport);
                        mi.missingSubjects.shift();
                        setMissingImport(mi);
                    }
                }
            ]}>
                <div>
                    Prosím vyplňte chybějící údaje pro předmět se zkratkou: {subjectAbbreviation}
                    <br />
                    <br />
                    <label htmlFor="subject_name">Název předmětu</label>
                    <input id="subject_name" type="text"></input>
                </div>
            </Popup>
        )
    }

    if (missingImport != null && missingImport.missingExaminers.length > 0) {
        const examinerSurname = missingImport.missingExaminers[0];

        return (
            <Popup open={true} options={[
                {
                    text: "OK",
                    onClick: async () => {
                        await uploadData(
                            {
                                prefix: (document.getElementById("import_examiner_prefix")! as HTMLInputElement).value,
                                name: (document.getElementById("import_examiner_name")! as HTMLInputElement).value,
                                surname: examinerSurname,
                                suffix: (document.getElementById("import_examiner_suffix")! as HTMLInputElement).value,
                            } as Teacher,
                            "teacher"
                        )

                        const mi = Object.assign({}, missingImport);
                        mi.missingExaminers.shift();
                        setMissingImport(mi);
                        if (mi.missingExaminers.length === 0)
                            setTimeout(props.onFinish, 0);
                    }
                }
            ]}>
                <div>
                    Prosím vyplňte chybějící údaje pro učitele s příjmením: {examinerSurname}
                    <br />
                    <br />
                    <label htmlFor="import_examiner_prefix">Prefix</label>
                    <input id="import_examiner_prefix" type="text"></input>
                    <br />
                    <br />
                    <label htmlFor="import_examiner_name">Křestní jméno</label>
                    <input id="import_examiner_name" type="text"></input>
                    <br />
                    <br />
                    <label htmlFor="import_examiner_suffix">Suffix</label>
                    <input id="import_examiner_suffix" type="text"></input>
                </div>
            </Popup>
        )
    }

    return (
        <Popup open={true} options={[]}>
            <ImportDropzone onDrop={handleUpload}>
                <p><FormattedMessage id="import.drop" /></p>
            </ImportDropzone>
        </Popup>
    );
}

export default Import;
