import {useContext, useState} from 'react';
import './DashboardTable.css';
import DashboardTableItem from './DashboardTableItem';
import {ExamsContext} from '../../contexts/ExamsContext';
import DashboardSearch from './DashboardSearch';
import {ExamDisplayRestrictions} from '../../models/ExamDisplayRestrictions';
import {isExamNH, isExamOZ} from '../../services/ExamService';
import FilterOptions from './FilterOptions';
import {Period, PeriodContext} from '../../contexts/PeriodContext';
import {formatClassRelativeToPeriod} from '../../services/_ClassService';
import {formatTeacher} from '../../services/TeacherService';
import {formatStudent} from '../../services/StudentService';
import Exam, {FinalMarkType} from '../../models/data/Exam';

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
    if (examDisplayRestrictions.filter.type === 'nahradni_hodnoceni' && isExamOZ(exam)) {
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

function getFinalMarkSortValue(finalMark: FinalMarkType): number {
  if (finalMark === null) {
    return 6;
  }
  return Number(finalMark);
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

  let sortPredicate;
  if (examDisplayRestrictions.sortBy === 'student._class') {
    sortPredicate = (exam1: Exam, exam2: Exam) => formatClassRelativeToPeriod(exam1.student._class, period, false).localeCompare(formatClassRelativeToPeriod(exam2.student._class, period, false));
  } else if (examDisplayRestrictions.sortBy === 'mark') {
    sortPredicate = (exam1: Exam, exam2: Exam) => getFinalMarkSortValue(exam1.finalMark) - getFinalMarkSortValue(exam2.finalMark);
  } else if (examDisplayRestrictions.sortBy === 'student') {
    sortPredicate = (exam1: Exam, exam2: Exam) => formatStudent(exam1.student).localeCompare(formatStudent(exam2.student));
  } else if (examDisplayRestrictions.sortBy === 'examiner') {
    sortPredicate = (exam1: Exam, exam2: Exam) => formatTeacher(exam1.examiner).localeCompare(formatTeacher(exam2.examiner));
  }
  if (sortPredicate !== null) {
    exams.sort(sortPredicate);
  }
  if (examDisplayRestrictions.reverse) {
    exams.reverse();
  }

  let renderedExams;
  if (examDisplayRestrictions.groupBy === undefined) {
    renderedExams = (
      <table className="dashboard_table">
        <tbody>
          {exams.map(exam => (
            <DashboardTableItem key={exam.id} exam={exam} />
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
      <ul className="dasboard_list_group">
        {Object.entries(groupedExams).map(entry => {
          const [groupId, groupExams] = entry;

          // TODO add group specific metadata
          let groupElement;
          if (examDisplayRestrictions.groupBy === 'examiner') {
            groupElement = (
              <div className="dasboard_list_group_header">
                <h3>{formatTeacher(groupExams[0].examiner)}</h3>
              </div>
            );
          } else if (examDisplayRestrictions.groupBy === 'student') {
            groupElement = (
              <div className="dasboard_list_group_header">
                <h3>{formatStudent(groupExams[0].student)} ({formatClassRelativeToPeriod(groupExams[0].student._class, period)})</h3>
              </div>
            );
          } else if (examDisplayRestrictions.groupBy === 'student._class') {
            groupElement = (
              <div className="dasboard_list_group_header">
                <h3>{formatClassRelativeToPeriod(groupExams[0].student._class, period)}</h3>
              </div>
            );
          }

          return (
            <li key={groupId}>
              {groupElement}
              <table className="dashboard_table">
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

      <div className="dashboard_record_count_container">
        <p>Zobrazeno {exams.length} záznamů</p>
      </div>

      {renderedExams}
    </>
  );
};

export default DashboardTable;
