import { FormattedMessage } from "react-intl";
import Popup from "../Popup";
import ImportDropzone from "./ImportDropzone";
import { useEffect } from "react";
import Subject from "../../models/data/Subject";
import Teacher from "../../models/data/Teacher";
import { uploadCsvFile, uploadData } from "../../ApiClient";
import { ImportPhase } from "../../models/ImportPhase";


const Import = (props: {
    phase: ImportPhase,
    setPhase: (phase: ImportPhase) => void
}) => {
    useEffect(() => {
        if (props.phase.phase === "2_UPLOADING") {
            uploadCsvFile(props.phase.data).then(response => {
                if (props.phase.phase !== "2_UPLOADING")
                    throw new Error("Invalid state: import phase changed while uploading data.");

                if (response === "")
                    props.setPhase({
                        phase: "5_SUCCESS",
                    });
                else
                    props.setPhase({
                        phase: "3_MISSING_EXAMINERS",
                        data: props.phase.data,
                        missingExaminers: response.missingExaminers,
                        missingSubjects: response.missingSubjects
                    });
            });
        }

        else if (props.phase.phase === "3_MISSING_EXAMINERS") {
            if (props.phase.missingExaminers.length === 0) {
                props.setPhase({
                    phase: "4_MISSING_SUBJECTS",
                    data: props.phase.data,
                    missingExaminers: props.phase.missingExaminers,
                    missingSubjects: props.phase.missingSubjects
                });
            }
        }

        else if (props.phase.phase === "4_MISSING_SUBJECTS") {
            if (props.phase.missingSubjects.length === 0) {
                props.setPhase({
                    phase: "2_UPLOADING",
                    data: props.phase.data
                });
            }
        }

    }, [props.phase]);



    if (props.phase.phase === "1_UPLOAD") {
        function handleUpload(data: ArrayBuffer) {
            props.setPhase({
                phase: "2_UPLOADING",
                data
            });
        }

        return (
            <Popup open={true} options={[]}>
                <ImportDropzone onDrop={handleUpload}>
                    <h3><FormattedMessage id="import.drop" /></h3>
                </ImportDropzone>
            </Popup>
        );
    }

    if (props.phase.phase === "2_UPLOADING") {
        return (
            <Popup open={true} options={[]}>
                <h3>Nahrávání...</h3>
            </Popup>
        );
    }

    if (props.phase.phase === "3_MISSING_EXAMINERS") {
        const examinerSurname = props.phase.missingExaminers[0];

        return (
            <Popup open={true} options={[
                {
                    text: "OK",
                    onClick: async () => {
                        const examinerPrefixEl = (document.getElementById("import_examiner_prefix")! as HTMLInputElement);
                        const examinerNameEl = (document.getElementById("import_examiner_name")! as HTMLInputElement);
                        const examinerSuffixEl = (document.getElementById("import_examiner_suffix")! as HTMLInputElement);

                        await uploadData(
                            {
                                prefix: examinerPrefixEl.value,
                                name: examinerNameEl.value,
                                surname: examinerSurname,
                                suffix: examinerSuffixEl.value,
                            } as Teacher,
                            "teacher"
                        );

                        if (props.phase.phase !== "3_MISSING_EXAMINERS")
                            throw new Error("Invalid state: import phase changed while uploading data.");

                        examinerPrefixEl.value = "";
                        examinerNameEl.value = "";
                        examinerSuffixEl.value = "";

                        const newPhase = Object.assign({}, props.phase);
                        newPhase.missingExaminers.shift();
                        props.setPhase(newPhase);
                    }
                }
            ]}>
                <div>
                    <h3>Prosím vyplňte chybějící údaje pro učitele s příjmením: {examinerSurname}</h3>
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

    if (props.phase.phase === "4_MISSING_SUBJECTS") {
        const subjectAbbreviation = props.phase.missingSubjects[0];

        return (
            <Popup open={true} options={[
                {
                    text: "OK",
                    onClick: async () => {
                        const subjectNameEl = (document.getElementById("subject_name")! as HTMLInputElement);

                        await uploadData(
                            {
                                name: subjectNameEl.value,
                                abbreviation: subjectAbbreviation
                            } as Subject,
                            "subject"
                        )

                        if (props.phase.phase !== "4_MISSING_SUBJECTS")
                            throw new Error("Invalid state: import phase changed while uploading data.");

                        subjectNameEl.value = "";

                        const newPhase = Object.assign({}, props.phase);
                        newPhase.missingSubjects.shift();
                        props.setPhase(newPhase);
                    }
                }
            ]}>
                <div>
                    <h3>Prosím vyplňte chybějící údaje pro předmět se zkratkou: {subjectAbbreviation}</h3>
                    <br />
                    <br />
                    <label htmlFor="subject_name">Název předmětu</label>
                    <input id="subject_name" type="text"></input>
                </div>
            </Popup>
        )
    }

    if (props.phase.phase === "5_SUCCESS") {
        return (
            <Popup open={true} options={[{
                text: "OK",
                onClick: () => props.setPhase({
                    phase: "0_CLOSED",
                })
            }]}>
                <h3>Import dat proběhl úspěšně</h3>
            </Popup>
        );
    }

    return (<></>);
}

export default Import;
