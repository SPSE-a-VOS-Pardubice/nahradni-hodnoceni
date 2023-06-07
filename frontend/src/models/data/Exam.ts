import Classroom from "./Classroom";
import Student from "./Student";
import Subject from "./Subject";
import Teacher from "./Teacher";

export default interface Exam {
    id: number;
    available: boolean;
    
    student: Student;
    subject: Subject;
    classroom: Classroom | null;
    chairman: Teacher | null;
    class_teacher: Teacher | null;
    examiner: Teacher;
    time: number | null; // TODO
    originalMark: string;
    finalMark: string | null;
}
