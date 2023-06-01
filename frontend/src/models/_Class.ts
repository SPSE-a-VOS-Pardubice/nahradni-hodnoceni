import Teacher from "./Teacher";

export default interface _Class {
    id: number;
    available: boolean;
    
    year: number;
    label: string;
    teacher: Teacher | null;
}
