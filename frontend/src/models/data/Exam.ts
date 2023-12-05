import Classroom from './Classroom';
import Student from './Student';
import Subject from './Subject';
import Teacher from './Teacher';

export type FinalMarkType = '1' | '2' | '3' | '4' | '5' | null;

interface Exam {
  id: number;
  available: boolean;

  student: Student;
  subject: Subject;
  classroom: Classroom | null;
  chairman: Teacher | null;
  class_teacher: Teacher | null;
  examiner: Teacher;
  time: number | null;
  year: number;
  period: number;
  originalMark: '5' | 'N';
  finalMark: FinalMarkType;
}

export default Exam;
