import {useContext} from 'react';
import {FormattedDate, FormattedMessage, FormattedTime} from 'react-intl';
import Exam from '../../models/data/Exam';
import _Class from '../../models/data/_Class';
import './DashboardTableItem.css';
import {PeriodContext} from '../../contexts/PeriodContext';
import {isExamNH, updateExam} from '../../services/ExamService';
import {ExamsContext} from '../../contexts/ExamsContext';
import {formatClassRelativeToPeriod} from '../../services/_ClassService';
import {formatTeacher} from '../../services/TeacherService';

const FormattedClass = (props: {
    _class: _Class
}) => {
  const period = useContext(PeriodContext).data;
  return (<>{formatClassRelativeToPeriod(props._class, period)}</>);
};

const FormattedMark = (props: {
    mark: string | null
}) => {
  return props.mark === null
    ? (<FormattedMessage id="mark.new.unknown" />)
    : (<>{props.mark} - <FormattedMessage id={`mark.${props.mark}`} /></>);
};

function getResultClassByMark(mark: string | null) {
  switch (mark) {
  case null:
    return 'unmarked';

  case '1':
  case '2':
  case '3':
  case '4':
    return 'succeeded';

  case '5':
    return 'failed';

  default:
    throw new Error(`neznámá známka "${mark}"`);
  }
}

const DashboardTableItem = (props: {
    exam: Exam
}) => {
  const examsContext = useContext(ExamsContext);
  // existence of exams is already checked in DashboardPage
  if (examsContext.id !== 'SUCCESS') {
    return <></>;
  }

  async function setMark(value: string | null) {
    if (examsContext.id !== 'SUCCESS') {
      console.error('exams must be available');
      return;
    }

    const newExam = structuredClone(props.exam);
    newExam.finalMark = value;
    await updateExam(examsContext.content, newExam);
  }

  let relevantButtons;
  if (props.exam.time === null) {
    relevantButtons = (
      <>
        <td></td>
        <td></td>
      </>
    );
  } else if (props.exam.time !== null && props.exam.finalMark === null) {
    relevantButtons = (
      <>
        <td>
          <button className="select" name="mark_student" id="mark_student">
            <span>Oznámkovat<i className="fa-solid fa-angle-down"></i></span>
            <div className="dropdown">
              {['1', '2', '3', '4', '5'].map(mark => (
                <option key={mark} value={mark} onClick={() => setMark(mark)}><FormattedMark mark={mark} /></option>
              ))}
              <option value="cancel" onClick={() => setMark(null)}><FormattedMessage id="mark.remove" /></option>
            </div>
          </button>
        </td>
        <td>
          <button className="select" name="form" id="form">
            <span>Formulář<i className="fa-solid fa-angle-down"></i></span>
            <div className="dropdown">
              <option value="0">Odevzdáno</option>
              <option value="1">Pošta</option>
              <option value="2">Neodevzdáno</option>
            </div>
          </button>
        </td>
      </>
    );
  } else {
    relevantButtons = (
      <>
        <td></td>
        <td></td>
      </>
    );
  }

  return (
    <tr className="dashboard_table_item">
      <td className="result">
        <span id={getResultClassByMark(props.exam.finalMark)} className="result_data"><FormattedMessage id={isExamNH(props.exam) ? 'exam.type.NH.short' : 'exam.type.OZ.short'} /></span>
      </td>
      <td className="student_and_class">
        <span className="student_name main_data">{props.exam.student.name} {props.exam.student.surname}</span><br />
        <span className="student_class sub_data"><FormattedClass _class={props.exam.student._class} /></span>
      </td>
      <td className="subject_teacher">
        <span className="subject main_data">({props.exam.subject.abbreviation}) {props.exam.subject.name}</span><br />
        <span className="teacher_name sub_data">{formatTeacher(props.exam.examiner)}</span>
      </td>
      <td className="date_school_room">
        <span className="date main_data">{props.exam.time === null ? (<FormattedMessage id="time.unknown" />) : ((<FormattedDate value={props.exam.time} />))}</span><br />
        <span className="time_room_data sub_data"><span className="time sub_data">{props.exam.time !== null && (<FormattedTime value={props.exam.time} />)}</span> <span className="school_room sub_data"><FormattedMessage id="classroom.short" /> {props.exam.classroom === null ? (<FormattedMessage id="classroom.unknown" />) : (props.exam.classroom.label)}</span></span>
      </td>
      <td className="new_mark">
        <span className="new_mark_text main_data">Nová známka</span><br />
        <span className="new_mark_value sub_data"><FormattedMark mark={props.exam.finalMark} /></span>
      </td>
      {relevantButtons}
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
  );
};

export default DashboardTableItem;