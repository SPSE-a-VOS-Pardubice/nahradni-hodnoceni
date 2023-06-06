import { useContext, useState } from 'react'
import './DashboardTableItem.css'
import Exam from '../../models/Exam'
import SelectedPeriod from '../../contexts/SelectedPeriod'
import { FormattedDate, FormattedMessage, FormattedTime } from 'react-intl'
import _Class from '../../models/_Class'
import { uploadExam } from '../../ApiClient'

const DashboardTableItemComponent = (props: {
    exam: Exam,
    onExamUpdate: (newExam: Exam) => void
}) => {

    async function setMark(value: string | null) {
        let newExam = Object.assign({}, props.exam);
        newExam.finalMark = value;
        newExam = await uploadExam(newExam);
        setTimeout(props.onExamUpdate, 0, newExam);
    }

    return (
        <tr className="dashboard_row">
            <td className="result">
                <span id={getResultClassByMark(props.exam.finalMark)} className="result_data"><FormattedMessage id={props.exam.originalMark === "5" ? "exam.type.5.short" : "exam.type.N.short"} /></span>
            </td>
            <td className="student_and_class">
                <span className="student_name main_data">{props.exam.student.name} {props.exam.student.surname}</span><br />
                <span className="student_class sub_data"><FormattedClass _class={props.exam.student._class} /></span>
                {/* <table className="all_exams">
                    <tr className="exam_row">
                        <td className="result">
                            <span id="unmarked" className="result_data">NH</span>
                        </td>
                        <td className="student_and_class">
                            <span className="student_name main_data">Drahokoupil Sebastian</span><br />
                            <span className="student_class sub_data">1.A</span>
                        </td>
                        <td className="subject_teacher">
                            <span className="subject main_data">(VT) Výpočetní technika</span><br />
                            <span className="teacher_name sub_data">Ing. Miroslav Koucký</span>
                        </td>
                        <td className="date_school_room">
                            <span className="date sub_data">18/02/2023</span><br />
                            <span className="time_room_data sub_data"><span className="time sub_data">08:55</span> <span className="school_room sub_data">uč. C214</span></span>
                        </td>
                        <td className="edit_delete">
                            <div className="edit_options">
                                <span className="edit_option">
                                    <a href="uprava.html"><i className="fa-solid fa-pen-to-square"></i></a>
                                </span>
                                <span className="delete_option">
                                    <i className="fa-solid fa-trash"></i>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr className="exam_row">
                        <td className="result">
                            <span id="unmarked" className="result_data">OZ</span>
                        </td>
                        <td className="student_and_class">
                            <span className="student_name main_data">Drahokoupil Sebastian</span><br />
                            <span className="student_class sub_data">1.A</span>
                        </td>
                        <td className="subject_teacher">
                            <span className="subject main_data">(MA) Matematika</span><br />
                            <span className="teacher_name sub_data">Mgr. Milan Michalec</span>
                        </td>
                        <td className="date_school_room">
                            <span className="date sub_data">Datum nezadáno</span><br />
                            <span className="time_room_data sub_data"><span className="time sub_data">Čas a</span> <span className="school_room sub_data">Místnost nezadáno</span></span>
                        </td>
                        <td className="edit_delete">
                            <div className="edit_options">
                                <span className="edit_option">
                                    <a href="uprava.html"><i className="fa-solid fa-pen-to-square"></i></a>
                                </span>
                                <span className="delete_option">
                                    <i className="fa-solid fa-trash"></i>
                                </span>
                            </div>
                        </td>
                    </tr>
                </table> */}
            </td>
            <td className="subject_teacher">
                <span className="subject main_data">({props.exam.subject.abbreviation}) {props.exam.subject.name}</span><br />
                <span className="teacher_name sub_data">{props.exam.examiner.prefix} {props.exam.examiner.name} {props.exam.examiner.surname} {props.exam.examiner.suffix}</span>
            </td>
            <td className="date_school_room">
                <span className="date main_data">{props.exam.time === null ? (<FormattedMessage id='time.unknown' />) : ((<FormattedDate value={props.exam.time} />))}</span><br />
                <span className="time_room_data sub_data"><span className="time sub_data">{props.exam.time !== null && (<FormattedTime value={props.exam.time} />)}</span> <span className="school_room sub_data"><FormattedMessage id="classroom.short" /> {props.exam.classroom === null ? (<FormattedMessage id="classroom.unknown" />) : (props.exam.classroom.label)}</span></span>
            </td>
            <td className="new_mark">
                <span className="new_mark_text main_data">Nová známka</span><br />
                <span className="new_mark_value sub_data"><FormattedMark mark={props.exam.finalMark} /></span>
            </td>
            <td className="add_mark_select">
                <button className="select" name="mark_student" id="mark_student">
                    <span>Oznámkovat<i className="fa-solid fa-angle-down"></i></span>
                    <div className="dropdown">
                        {["1", "2", "3", "4", "5"].map(mark => (
                            <option key={mark} value={mark} onClick={_ => setMark(mark)}><FormattedMark mark={mark} /></option>
                        ))}
                        <option value="cancel" onClick={_ => setMark(null)}><FormattedMessage id="mark.remove" /></option>
                    </div>
                </button>
            </td>
            <td className="form_select">
                <button className="select" name="form" id="form">
                    <span>Formulář<i className="fa-solid fa-angle-down"></i></span>
                    <div className="dropdown">
                        <option value="0">Odevzdáno</option>
                        <option value="1">Pošta</option>
                        <option value="2">Neodevzdáno</option>
                    </div>
                </button>
            </td>
            <td className="edit_delete">
                <div className="edit_options">
                    <span className="edit_option">
                        <a href="uprava.html"><i className="fa-solid fa-pen-to-square"></i></a>
                    </span>
                    <span className="delete_option">
                        <i className="fa-solid fa-trash"></i>
                    </span>
                </div>
            </td>
        </tr>
    )
}

const FormattedClass = (props: {
    _class: _Class
}) => {
    const selectedPeriod = useContext(SelectedPeriod);
    return (<>{selectedPeriod.year - props._class.year}.{props._class.label}</>)
}

const FormattedMark = (props: {
    mark: string | null
}) => {
    return props.mark === null ?
        (<FormattedMessage id='mark.new.unknown' />) :
        (<>{props.mark} - <FormattedMessage id={`mark.${props.mark}`} /></>)
}

function getResultClassByMark(mark: string | null) {
    switch (mark) {
        case null:
            return "unmarked";

        case "1":
        case "2":
        case "3":
        case "4":
            return "succeeded";
        
        case "5":
            return "failed";
    
        default:
            throw new Error(`neznámá známka "${mark}"`);
    }
}

export default DashboardTableItemComponent
