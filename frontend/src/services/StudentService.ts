import Student from '../models/data/Student';

export function formatStudent(student: Student): string {
  return `${student.name} ${student.surname}`;
}
