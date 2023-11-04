import {ChangeEvent, useContext, useEffect, useState} from 'react';
import {FormattedDate, FormattedMessage, FormattedTime} from 'react-intl';
import Exam from '../../models/data/Exam';
import _Class from '../../models/data/_Class';
import './DashboardTableItem.css';
import {PeriodContext} from '../../contexts/PeriodContext';
import {checkConflicts, isExamNH, updateExam} from '../../services/ExamService';
import {ExamsContext} from '../../contexts/ExamsContext';
import {formatClassRelativeToPeriod} from '../../services/_ClassService';
import {formatTeacher} from '../../services/TeacherService';
import classNames from 'classnames';

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
  const period = useContext(PeriodContext).data;
  const examsContext = useContext(ExamsContext);
  const [selectedTimestamp, setSelectedTimestamp] = useState<number | null>(null);
  const [buttonsOverride, setButtonsOverride] = useState<'dateAndLocation' | 'mark' | null>(null);
  const [studentTimeConflicts, setStudentTimeConflicts] = useState<boolean>(false);
  const [teacherTimeConflicts, setTeacherTimeConflicts] = useState<{onConfirm:() => void} | false>(false);

  useEffect(() => {
    setSelectedTimestamp(props.exam.time);
  }, [props.exam.time]);

  // existence of exams is already checked in DashboardPage
  if (examsContext.id !== 'SUCCESS') {
    return <></>;
  }

  async function setFinalMark(newFinalMark: string | null) {
    if (examsContext.id !== 'SUCCESS') {
      console.error('exams must be available');
      return;
    }

    setButtonsOverride(null);

    const newExam = structuredClone(props.exam);
    newExam.finalMark = newFinalMark;
    await updateExam(examsContext.content, newExam);
  }

  function setTime(newTime: number | null) {
    if (examsContext.id !== 'SUCCESS') {
      console.error('exams must be available');
      return;
    }

    const action = async () => {
      setButtonsOverride(null);
      setTeacherTimeConflicts(false);

      const newExam = structuredClone(props.exam);
      newExam.time = newTime;
      await updateExam(examsContext.content, newExam);
    };

    const {student: studentConflicts, examiner: examinerConflicts} = checkConflicts(examsContext.content, props.exam, newTime);

    if (studentConflicts) {
      setStudentTimeConflicts(true);
    } else if (examinerConflicts) {
      setTeacherTimeConflicts({onConfirm: action});
    } else {
      action();
    }
  }

  const isDateAndLocationRelevant = (props.exam.time === null) || ((period.period === 2) && (props.exam.classroom === null));
  const isMarkRelevant = !isDateAndLocationRelevant && (props.exam.finalMark === null);

  let relevantButtons;
  if (buttonsOverride === 'dateAndLocation' || (buttonsOverride === null && isDateAndLocationRelevant)) {
    if (studentTimeConflicts) {
      relevantButtons = (
        <>
          <td>
            konflikt času zkoušky u studenta
          </td>
          <td></td>
        </>
      );
    // eslint-disable-next-line no-negated-condition
    } else if (teacherTimeConflicts !== false) {
      relevantButtons = (
        <>
          <td>
            <button onClick={() => teacherTimeConflicts && teacherTimeConflicts.onConfirm()} className="universal_button secondary">pokračovat i přes konflikt času zkoušky u učitele</button>
          </td>
          <td></td>
        </>
      );
    } else {
      // TODO check if selected value is in range
      const examYear = period.year + 1;
      const examMinMonth = period.period === 1 ? '00' : '06';
      const examMaxMonth = period.period === 1 ? '05' : '11';

      const handleChange = (event: ChangeEvent<HTMLInputElement>) => {
        if (event.target.valueAsDate === null) {
          setSelectedTimestamp(null);
          return;
        }

        const date = event.target.valueAsDate;
        date.setMinutes(date.getMinutes() + date.getTimezoneOffset());
        setSelectedTimestamp(date.getTime());
      };

      let defaultValue;
      if (props.exam.time !== null) {
      // https://stackoverflow.com/a/61082536/14693511
        const date = new Date(props.exam.time);
        date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
        defaultValue = date.toISOString().slice(0, 16);
      }

      relevantButtons = (
        <>
          <td className="dashboard_table_item_datetime_container">
            <input type="datetime-local" defaultValue={defaultValue} onChange={handleChange} step={3600} min={`${examYear}-${examMinMonth}-01T00:00`} max={`${examYear}-${examMaxMonth}-01T00:00`}></input>
            <button
              onClick={() => setTime(selectedTimestamp)}
              disabled={props.exam.time === selectedTimestamp}
              className={classNames(
                'dashboard_table_item_datetime_submit fa_button',
                {
                // eslint-disable-next-line camelcase
                  fa_button_highlight: props.exam.time !== selectedTimestamp,
                },
              )}>
              <i className="fa-solid fa-pen-to-square"></i>
            </button>
          </td>
          <td></td>
        </>
      );
    }
  } else if (buttonsOverride === 'mark' || (buttonsOverride === null && isMarkRelevant)) {
    relevantButtons = (
      <>
        <td>
          <button className="select" name="mark_student" id="mark_student">
            <span>Oznámkovat<i className="fa-solid fa-angle-down"></i></span>
            <div className="dropdown">
              {['1', '2', '3', '4', '5'].map(mark => (
                <option key={mark} value={mark} onClick={() => setFinalMark(mark)}><FormattedMark mark={mark} /></option>
              ))}
              <option value="cancel" onClick={() => setFinalMark(null)}><FormattedMessage id="mark.remove" /></option>
            </div>
          </button>
        </td>
        <td></td>
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
      <td className="dashboard_table_item_menu">
        {!studentTimeConflicts && !teacherTimeConflicts && (buttonsOverride === null)
          ? (
            <button className="select">
              <span>Seznam akcí</span>
              <div className="dropdown">
                {!isDateAndLocationRelevant && <option onClick={() => setButtonsOverride('dateAndLocation')}>{period.period === 1 ? 'datum' : 'učebna/datum'}</option>}
                {!isMarkRelevant && <option onClick={() => setButtonsOverride('mark')}>známka</option>}
              </div>
            </button>
          )
          : (
            <button className="fa_button">
              <i className="fa-solid fa-xmark" onClick={() => {
                // the close button should reset all states
                setStudentTimeConflicts(false);
                setTeacherTimeConflicts(false);
                setSelectedTimestamp(props.exam.time);
                setButtonsOverride(null);
              }}></i>
            </button>
          )
        }
      </td>
    </tr>
  );
};

export default DashboardTableItem;
