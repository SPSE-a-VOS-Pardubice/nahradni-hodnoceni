import _Class from "./_Class";

export default interface Student {
    id: number;
    available: boolean;
    
    name: string;
    surname: string;
    _class: _Class;
}
