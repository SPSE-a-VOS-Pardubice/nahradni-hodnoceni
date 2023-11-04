import {useContext, useState} from 'react';
import './DashboardTable.css';
import DashboardTableItem from './DashboardTableItem';
import {ExamsContext} from '../../contexts/ExamsContext';
import DashboardSearch from './DashboardSearch';
import {ExamDisplayRestrictions} from '../../models/ExamDisplayRestrictions';
import {isExamNH} from '../../services/ExamService';
import Exam from '../../models/data/Exam';
import FilterOptions from './FilterOptions';
import {Period, PeriodContext} from '../../contexts/PeriodContext';
import {formatClassRelativeToPeriod} from '../../services/_ClassService';
import {formatTeacher} from '../../services/TeacherService';
import {formatStudent} from '../../services/StudentService';

function applyFilter(examDisplayRestrictions: ExamDisplayRestrictions, exams: Exam[]) {
  return exams.filter(exam => {
    // filter.mark
    if (examDisplayRestrictions.filter.mark !== undefined && examDisplayRestrictions.filter.mark !== exam.finalMark) {
      return false;
    }
    // filter.status
    if (examDisplayRestrictions.filter.status === 'finished' && exam.finalMark === null) {
      return false;
    }
    if (examDisplayRestrictions.filter.status === 'unfinished' && exam.finalMark !== null) {
      return false;
    }
    // filter.success
    if (examDisplayRestrictions.filter.success === 'successful' && (exam.finalMark === null || exam.finalMark === '5')) {
      return false;
    }
    if (examDisplayRestrictions.filter.success === 'failed' && exam.finalMark !== '5') {
      return false;
    }
    // filter.type
    if (examDisplayRestrictions.filter.type === 'nahradni_hodnoceni' && !isExamNH(exam)) {
      return false;
    }
    if (examDisplayRestrictions.filter.type === 'opravna_zkouska' && isExamNH(exam)) {
      return false;
    }

    return true;
  });
}

// https://stackoverflow.com/a/37511463/14693511
function normalizeToken(token: string): string {
  return token
    .normalize('NFD')
    .replace(/\p{Diacritic}/gu, '')
    .toLowerCase();
}

const daysOfWeek = ['nedele', 'pondeli', 'utery', 'streda', 'ctvrtek', 'patek', 'sobota'];

function applySearch(period: Period, examDisplayRestrictions: ExamDisplayRestrictions, exams: Exam[]): Exam[] {
  if (examDisplayRestrictions.searchFor === '') {
    return exams;
  }

  const tokens = examDisplayRestrictions.searchFor.split(' ').map(normalizeToken);

  return exams.filter(
    exam => tokens.every(
      token => {
        if (normalizeToken(exam.student.name).includes(token)) {
          return true;
        }
        if (normalizeToken(exam.student.surname).includes(token)) {
          return true;
        }

        if (normalizeToken(exam.examiner.name).includes(token)) {
          return true;
        }
        if (normalizeToken(exam.examiner.surname).includes(token)) {
          return true;
        }

        if (normalizeToken(formatClassRelativeToPeriod(exam.student._class, period)).includes(token)) {
          return true;
        }
        if (normalizeToken(formatClassRelativeToPeriod(exam.student._class, period, false)).includes(token)) {
          return true;
        }
        if (exam.classroom && normalizeToken(exam.classroom.label).includes(token)) {
          return true;
        }

        if (exam.time) {
          const date = new Date(exam.time);
          if (date.getDate().toString() === token) {
            return true;
          }
          if (daysOfWeek[date.getDay()].includes(token)) {
            return true;
          }
        }

        return false;
      },
    ),
  );
}

const DashboardTable = () => {
  const examsContext = useContext(ExamsContext);
  const period = useContext(PeriodContext).data;

  const [examDisplayRestrictions, setExamDisplayRestrictions] = useState(new ExamDisplayRestrictions());

  // existence of exams is already checked in DashboardPage
  if (examsContext.id !== 'SUCCESS') {
    return <></>;
  }
  let exams = examsContext.content.data;

  exams = applyFilter(examDisplayRestrictions, exams);
  exams = applySearch(period, examDisplayRestrictions, exams);
  // TODO sort
  // TODO reverse

  let renderedExams;
  if (examDisplayRestrictions.groupBy === undefined) {
    const newExamDisplayRestrictions = structuredClone(examDisplayRestrictions);
    newExamDisplayRestrictions.groupBy = 'examiner';
    setExamDisplayRestrictions(newExamDisplayRestrictions);

    renderedExams = (
      <table className="dashboard">
        <tbody>
          {exams.map((exam, i) => (
            <DashboardTableItem key={i} exam={exam} />
          ))}
        </tbody>
      </table>
    );
  } else {
    let predicate;
    switch (examDisplayRestrictions.groupBy) {
    case 'examiner':
    case 'student':
      predicate = (exam: Exam) => exam[examDisplayRestrictions.groupBy as 'examiner' | 'student'].id;
      break;
    case 'student._class':
      predicate = (exam: Exam) => exam.student._class.id;
      break;
    }

    // TODO add polyfill for Object.groupBy
    const groupedExams = Object.groupBy(exams, predicate) as {[id: number]: Exam[]};

    renderedExams = (
      <ul>
        {Object.entries(groupedExams).map(entry => {
          const [groupId, groupExams] = entry;

          // TODO add group specific metadata
          let groupElement;
          if (examDisplayRestrictions.groupBy === 'examiner') {
            groupElement = (
              <div>
                <h3>{formatTeacher(groupExams[0].examiner)}</h3>
              </div>
            );
          } else if (examDisplayRestrictions.groupBy === 'student') {
            groupElement = (
              <div>
                <h3>{formatStudent(groupExams[0].student)}</h3>
              </div>
            );
          } else if (examDisplayRestrictions.groupBy === 'student._class') {
            groupElement = (
              <div>
                <h3>{formatClassRelativeToPeriod(groupExams[0].student._class, period)}</h3>
              </div>
            );
          }

          return (
            <li key={groupId}>
              {groupElement}
              <table className="dashboard">
                <tbody>
                  {groupExams.map((exam, i) => (
                    <DashboardTableItem key={i} exam={exam} />
                  ))}
                </tbody>
              </table>
            </li>
          );
        })}
      </ul>
    );
  }

  const setSearchFor = (newSearchFor: string) => {
    const newExamDisplayRestrictions = structuredClone(examDisplayRestrictions);
    newExamDisplayRestrictions.searchFor = newSearchFor;
    setExamDisplayRestrictions(newExamDisplayRestrictions);
  };

  return (
    <>
      <DashboardSearch searchFor={examDisplayRestrictions.searchFor} setSearchFor={setSearchFor} />
      <FilterOptions restrictions={examDisplayRestrictions} setRestrictions={setExamDisplayRestrictions} />

      <div className="num_of_records_part">
        <p>Zobrazeno {exams.length} záznamů</p>
      </div>

      {renderedExams}

      <div className="delete_popup popup">
        <div className="popup_container">
          <p>
            Opravdu si přejete smazat náhradní hodnocení{' '}
            <span className="student_name"></span>
          </p>
          <div className="decision_row">
            <button id="yes_delete">ANO</button>
            <button id="no_delete">NE</button>
          </div>
        </div>
      </div>

      <div className="repair_popup popup">
        <div className="popup_container">
          <p>
            Chcete vytvořit opravnou zkoušku pro{' '}
            <span className="student_name"></span>
          </p>
          <div className="decision_row">
            <button id="yes_repair">
              <a href="/editExamp">ANO</a>
            </button>
            <button id="no_repair">NE</button>
          </div>
        </div>
      </div>
    </>
  );
};

export default DashboardTable;
