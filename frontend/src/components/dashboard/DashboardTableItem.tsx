import {ChangeEvent, useContext, useEffect, useState} from 'react';
import {FormattedDate, FormattedMessage, FormattedTime} from 'react-intl';
import Exam, {FinalMarkType} from '../../models/data/Exam';
import _Class from '../../models/data/_Class';
import './DashboardTableItem.css';
import {PeriodContext} from '../../contexts/PeriodContext';
import {checkConflicts, createExam, deleteExam, isExamNH, updateExam} from '../../services/ExamService';
import {ExamsContext} from '../../contexts/ExamsContext';
import {formatClassRelativeToPeriod} from '../../services/_ClassService';
import {formatTeacher} from '../../services/TeacherService';
import classNames from 'classnames';
import Combobox from '../ui/Combobox';
import Teacher from '@/models/data/Teacher';
import {TeachersContext} from '@/contexts/TeachersContext';

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

enum EditGroup {
  ChairmanAndClassTeacher,
  DateAndTime,
  Mark,
}

const DashboardTableItem = (props: {
    exam: Exam
}) => {
  const period = useContext(PeriodContext).data;
  const examsContext = useContext(ExamsContext);
  const teachersContext = useContext(TeachersContext);
  const [selectedTimestamp, setSelectedTimestamp] = useState<number | null>(null);
  const [selectedChairman, setSelectedChairman] = useState<Teacher | null>(null);
  const [selectedClassTeacher, setSelectedClassTeacher] = useState<Teacher | null>(null);
  const [editGroupOverride, setEditGroupOverride] = useState<EditGroup | null>(null);
  const [studentTimeConflicts, setStudentTimeConflicts] = useState<boolean>(false);
  const [examinerTimeConflicts, setExaminerTimeConflicts] = useState<{onConfirm:() => void} | false>(false);

  useEffect(() => {
    setSelectedTimestamp(props.exam.time);
  }, [props.exam.time]);

  // existence of exams is already checked in DashboardPage
  if (examsContext.id !== 'SUCCESS' || teachersContext.id !== 'SUCCESS') {
    return <></>;
  }

  async function setFinalMark(newFinalMark: FinalMarkType) {
    if (examsContext.id !== 'SUCCESS') {
      console.error('exams must be available');
      return;
    }

    setEditGroupOverride(null);

    const newExam = structuredClone(props.exam);
    newExam.finalMark = newFinalMark;
    await updateExam(examsContext.content, newExam);

    // TODO updateExam's change doesn't update examsContext.content at this point so calling createExam uses the old exams list
    if (isExamNH(props.exam) && newFinalMark === '5' && examsContext.content.data.filter(exam => exam.student.id === props.exam.student.id && exam.finalMark === '5').length < 2) {
      await createExam(
        examsContext.content,
        {
          examiner: props.exam.examiner,
          originalMark: '5',
          student: props.exam.student,
          subject: props.exam.subject,
          year: props.exam.year,
          period: props.exam.period,
        } as Exam,
      );
    }
  }

  function setTime(newTime: number | null) {
    if (examsContext.id !== 'SUCCESS') {
      console.error('exams must be available');
      return;
    }

    const action = async () => {
      setEditGroupOverride(null);
      setExaminerTimeConflicts(false);

      const newExam = structuredClone(props.exam);
      newExam.time = newTime;
      await updateExam(examsContext.content, newExam);
    };

    const {student: studentConflicts, examiner: examinerConflicts} = checkConflicts(examsContext.content, props.exam, newTime);

    if (studentConflicts) {
      setStudentTimeConflicts(true);
    } else if (examinerConflicts) {
      setExaminerTimeConflicts({onConfirm: action});
    } else {
      action();
    }
  }

  async function removeExam() {
    if (examsContext.id !== 'SUCCESS') {
      console.error('exams must be available');
      return;
    }

    await deleteExam(examsContext.content, props.exam.id);
  }

  async function setChairmanAndClassTeacher(newChairman: Teacher | null, newClassTeacher: Teacher | null) {
    console.log(newChairman, newClassTeacher);
  }

  let editGroup;
  if (editGroupOverride === null) {
    // find relevant edit group
    if (period.period === 1 && props.exam.time === null) {
      editGroup = EditGroup.DateAndTime;
    } else if (period.period === 1 && isExamNH(props.exam) && (props.exam.chairman === null || props.exam.class_teacher)) {
      editGroup = EditGroup.ChairmanAndClassTeacher;
    } else if (props.exam.finalMark === null) {
      editGroup = EditGroup.Mark;
    }
  } else {
    editGroup = editGroupOverride;
  }

  let editGroupButtons;
  if (editGroup === EditGroup.ChairmanAndClassTeacher) {
    editGroupButtons = (
      <>
        <td>
          <Combobox selectTarget="přísedícího" data={teachersContext.content.data.map(formatTeacher)} onChange={console.log} />
        </td>
        <td className="dashboard_table_item_class_teacher_container">
          <Combobox selectTarget="předsedu" data={teachersContext.content.data.map(formatTeacher)} onChange={console.log} />
          <button
            onClick={() => setChairmanAndClassTeacher(selectedChairman, selectedClassTeacher)}
            disabled={props.exam.chairman === selectedChairman && props.exam.class_teacher === selectedClassTeacher}
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
      </>
    );
  } else if (editGroup === EditGroup.DateAndTime) {
    if (studentTimeConflicts) {
      editGroupButtons = (
        <>
          <td>
            konflikt času zkoušky u studenta
          </td>
          <td></td>
        </>
      );
    // eslint-disable-next-line no-negated-condition
    } else if (examinerTimeConflicts !== false) {
      editGroupButtons = (
        <>
          <td>
            <button onClick={() => examinerTimeConflicts && examinerTimeConflicts.onConfirm()} className="universal_button secondary">pokračovat i přes konflikt času zkoušky u zkoušejícího</button>
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

      editGroupButtons = (
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
  } else if (editGroup === EditGroup.Mark) {
    editGroupButtons = (
      <>
        <td>
          <button className="select" name="mark_student" id="mark_student">
            <span>Oznámkovat<i className="fa-solid fa-angle-down"></i></span>
            <div className="dropdown">
              {(['1', '2', '3', '4', '5'] as FinalMarkType[]).map(mark => (
                <option key={mark} value={mark!} onClick={() => setFinalMark(mark)}><FormattedMark mark={mark} /></option>
              ))}
              <option value="cancel" onClick={() => setFinalMark(null)}><FormattedMessage id="mark.remove" /></option>
            </div>
          </button>
        </td>
        <td></td>
      </>
    );
  } else {
    editGroupButtons = (
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
      {editGroupButtons}
      <td className="dashboard_table_item_menu">
        {!studentTimeConflicts && !examinerTimeConflicts && (editGroupOverride === null)
          ? (
            <button className="select">
              <span>Seznam akcí</span>
              <div className="dropdown">
                {editGroup !== EditGroup.DateAndTime && <option onClick={() => setEditGroupOverride(EditGroup.DateAndTime)}>datum</option>}
                {editGroup !== EditGroup.ChairmanAndClassTeacher && <option onClick={() => setEditGroupOverride(EditGroup.ChairmanAndClassTeacher)}>komise</option>}
                {editGroup !== EditGroup.Mark && <option onClick={() => setEditGroupOverride(EditGroup.Mark)}>známka</option>}
                <option onClick={() => removeExam()}>smazat</option>
              </div>
            </button>
          )
          : (
            <button className="fa_button">
              <i className="fa-solid fa-xmark" onClick={() => {
                // the close button should reset all states
                setStudentTimeConflicts(false);
                setExaminerTimeConflicts(false);
                setSelectedTimestamp(props.exam.time);
                setEditGroupOverride(null);
              }}></i>
            </button>
          )
        }
      </td>
    </tr>
  );
};

export default DashboardTableItem;
