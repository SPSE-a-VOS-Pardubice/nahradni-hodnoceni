import Teacher from './Teacher';

interface _Class {
    id: number;
    available: boolean;

    year: number;
    label: string;
    teacher: Teacher | null;
}

export default _Class;
