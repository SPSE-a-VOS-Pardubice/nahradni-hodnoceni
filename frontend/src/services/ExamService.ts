import Exam from '../models/data/Exam';
import {uploadData} from './APIService';
import {ExamsContextType} from '../contexts/ExamsContext';

export function isExamNH(exam: Exam) {
  return exam.originalMark === 'N';
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
  setTimeout(examsContext.setData, 0, newExams);
}
