import { useEffect, useState } from 'react'
import DashBoardOption from './DashBoardOption'
import './DashBoardRow.css'
import AllExamsTable from './AllExamsTable'

const marksOptions = [
    {
        val: 0,
        display: '1 - Výborný'
    }, {
        val: 1,
        display: '2 - Chvalitebný'
    }, {
        val: 2,
        display: '3 - Dobrý'
    }, {
        val: 3,
        display: '4 - Dostatečný'
    }, {
        val: 4,
        display: '5 - Nedostatečný'
    }
]

const formOptions = [
    {
        val: 0,
        display: 'Odevzdáno'
    }, {
        val: 1,
        display: 'Pošta'
    }, {
        val: 2,
        display: 'Neodevzdáno'
    }
]

const DashBoardRow = (props) => {

    const [allExamsTable, setAllExams] = useState(<></>)

    const newMark = <td className="new_mark">
        <span className="new_mark_text main_data">Nová známka</span> <br />
        <span className="new_mark_value sub_data">{exapm.finalMark}</span>
    </td>


    useEffect(() => {
        if (props.showAllExams) {
            setAllExams(<AllExamsTable />)
        }
    }, [props.showAllExams])

    const exapm = props.exapm

    return (
        <tr key={exapm.id} className="dashboard_row">
            <td className="result">
                <span id="unmarked" className="result_data">{props.isNH ? 'NH' : 'OZ'}</span>
            </td>
            <td className="student_and_class">
                <span className="student_name main_data">{`${exapm.student.surname} ${exapm.student.name}`}</span> <br />
                <span className="student_class sub_data">{`${getYear(exapm)}${exapm.student._class.label}`}</span>
                {allExamsTable}
            </td>
            <td className="subject_teacher">
                <span className="subject main_data">{`(${exapm.subject.abbereviation}) ${exapm.subject.name}`}</span> <br />
                <span className="teacher_name sub_data">{`${formatTeacer(exapm.teacher)}`}</span>
            </td>
            <td className="date_school_room">
                <span className="date main_data">{formatDate(exapm.time)}</span> <br />
                <span className="time_room_data sub_data"><span class="time sub_data">{formatTime(exapm.time())}</span> <span
                    className="school_room sub_data">uč. {getClassroom(exapm.classroom)}</span></span>
            </td>

            {props.showAllExams ? newMark : <></>}

            {props.showAllExams ? <DashBoardOption tdClass={"add_mark_select"} btnName={"mark_student"} label={"Oznámkovat"} options={marksOptions} /> : <></>}

            {props.showAllExams ? <DashBoardOption tdClass={"form_select"} btnName={"form"} label={"Formulář"} options={formOptions} /> : <></>}


            <td className="edit_delete">
                <div className="edit_options">
                    <span className="edit_option">
                        <a href="/editExamp"><i className="fa-solid fa-pen-to-square"></i></a>
                    </span>
                    <span className="delete_option">
                        <i className="fa-solid fa-trash"></i>
                    </span>
                </div>
            </td>


        </tr>
    )
}

export default DashBoardRow

function getYear(exapm) {
    let startYear = exapm.student._class.year
    let actualYear = new Date().getFullYear()

    return Math.max(1, actualYear - startYear);
}

function formatTeacer(teacher) {

    if (teacher !== null) {
        let prefix = teacher.prefix
        let surname = teacher.surname
        let name = teacher.name
        let sufix = teacher.sufix

        return `${prefix} ${surname} ${name} ${sufix}`
    } else
        return ''
}

function formatDate(date) {
    if (date !== null) {
        return date.getDate() + '/' + date.getMonth() + '/' + date.getFullYear()
    } else
        return ''
}

function formatTime(time) {
    if (time !== null) {
        return time.getHours() + ':' + time.getMinutes()
    } else
        return ''
}

function getClassroom(classroom) {
    if (classroom !== null) {
        return classroom.label
    } else
        return null
}
