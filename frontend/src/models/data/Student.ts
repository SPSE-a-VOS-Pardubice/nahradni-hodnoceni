import _Class from './_Class';

interface Student {
    id: number;
    available: boolean;

    name: string;
    surname: string;
    _class: _Class;
}

export default Student;
