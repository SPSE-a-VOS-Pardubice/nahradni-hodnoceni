
export type GroupByOptions = 'student' | 'examiner' | 'student._class';
export type SortByOptions = GroupByOptions | 'mark';

export class Filter {
  status?: 'finished' | 'unfinished';
  type?: 'nahradni_hodnoceni' | 'opravna_zkouska';
  success?: 'successful' | 'failed';
  mark?: '1' | '2' | '3' | '4' | '5';
}

export class ExamDisplayRestrictions {
  filter = new Filter();
  searchFor = '';
  sortBy?: SortByOptions;
  groupBy?: GroupByOptions;
  reverse?: boolean;
}
