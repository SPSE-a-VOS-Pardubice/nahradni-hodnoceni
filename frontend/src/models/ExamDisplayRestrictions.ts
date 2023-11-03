
export type DataOptions = 'student' | 'teacher' | 'class' | 'mark';

export class Filter {
  status?: 'finished' | 'unfinished';
  type?: 'nahradni_hodnoceni' | 'opravna_zkouska';
  success?: 'successful' | 'failed';
  mark?: '1' | '2' | '3' | '4' | '5';
}

export class ExamDisplayRestrictions {
  filter = new Filter();
  searchFor = '';
  sortBy?: DataOptions;
  groupBy?: DataOptions;
  reverse?: boolean;
}
