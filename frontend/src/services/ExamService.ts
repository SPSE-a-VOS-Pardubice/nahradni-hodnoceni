import Exam from '../models/data/Exam';
import {uploadData} from './APIService';
import {ExamsContextType} from '../contexts/ExamsContext';

export const EXAM_DURATION = 1 * 60 * 60 * 1000;

export function isExamNH(exam: Exam) {
  return exam.originalMark === 'N';
}

export function isExamOZ(exam: Exam) {
  return exam.originalMark === '5';
}

export async function createExam(examsContext: ExamsContextType, newExam: Exam) {
  // update data on the server
  const newExamWithId = await uploadData(newExam, 'exam');

  const newExams = structuredClone(examsContext.data);
  newExams.push(newExamWithId);
  examsContext.setData(newExams);
}

/**
 * Aktualizuje data na serveru i lokálně.
 * @param examsContext
 * @param newExam
 */
export async function updateExam(examsContext: ExamsContextType, newExam: Exam) {
  // update data on the server
  await uploadData(newExam, 'exam');

  // update data locally
  const newExams = structuredClone(examsContext.data)
    .map(exam => exam.id === newExam.id ? newExam : exam);
  examsContext.setData(newExams);
}

export function checkConflicts(examsContext: ExamsContextType, previousExam: Exam, newTime: number | null): { examiner: boolean, student: boolean } {
  if (newTime === null) {
    return {examiner: false, student: false};
  }

  const examsWithoutPreviousExam = examsContext.data.filter(exam => exam.id !== previousExam.id);

  const conflictingStudent = examsWithoutPreviousExam.find(
    exam => (exam.student.id === previousExam.student.id) && exam.time !== null && (Math.abs(exam.time - newTime) < EXAM_DURATION),
  );
  if (conflictingStudent) {
    return {examiner: false, student: true};
  }

  const conflictingExaminer = examsWithoutPreviousExam.find(
    exam => (exam.examiner.id === previousExam.examiner.id) && exam.time !== null && (Math.abs(exam.time - newTime) < EXAM_DURATION),
  );
  if (conflictingExaminer) {
    return {examiner: true, student: false};
  }

  return {examiner: false, student: false};
}
