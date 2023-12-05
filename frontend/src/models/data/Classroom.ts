import Trait from './Trait';

interface Classroom {
  id: number;
  available: boolean;

  label: string;
  traits: Trait[]
}

export default Classroom;
