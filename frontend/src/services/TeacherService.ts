import Teacher from '../models/data/Teacher';

export function formatTeacher(teacher: Teacher): string {
  return `${teacher.prefix} ${teacher.name} ${teacher.surname} ${teacher.suffix}`;
}
