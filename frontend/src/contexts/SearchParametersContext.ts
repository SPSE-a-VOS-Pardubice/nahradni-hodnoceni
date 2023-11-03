import {createContext} from 'react';

export type DataOptions = 'student' | 'teacher' | 'class' | 'mark';

export class FilterParameters {
  status?: 'finished' | 'unfinished';
  type?: 'nahradni_hodnoceni' | 'opravna_zkouska';
  success?: 'successful' | 'failed';
  mark?: '1' | '2' | '3' | '4' | '5';
  text?: string;
}

export class SortParameters {
  by?: DataOptions;
}

export class GroupParameters {
  by?: DataOptions;
}

export type SearchParametersType = {
  filter: FilterParameters;
  sort: SortParameters;
  group: GroupParameters;

  reverse?: boolean;
};

const SearchParametersContext = createContext<SearchParametersType>({
  filter: new FilterParameters(),
  sort: new SortParameters(),
  group: new GroupParameters(),
});

export default SearchParametersContext;
