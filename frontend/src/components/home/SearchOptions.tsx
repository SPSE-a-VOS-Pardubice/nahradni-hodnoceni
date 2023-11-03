import './FilterOptions.css';

import FilterOption from './FilterOption';
import SortOption from './SortOption';
import {DataOptions, SearchParametersType} from '../../contexts/SearchParametersContext';

function propsToMapWithPrefix(prefix: string, props: string[]) {
  return Object.fromEntries(
    props.map(entry => [entry, `${prefix}${entry}`]),
  );
}

const statusOptions = propsToMapWithPrefix('filter.status.', [
  'finished',
  'unfinished',
]);

const typeOptions = propsToMapWithPrefix('filter.type.', [
  'nahradni_hodnoceni',
  'opravna_zkouska',
]);

const successOptions = propsToMapWithPrefix('filter.success.', [
  'successful',
  'failed',
]);

// const markOptions = {
//   1: '1 - Výborný',
//   2: '2 - Chvalitebný',
//   3: '3 - Dobrý',
//   4: '4 - Dostatečný',
//   5: '5 - Nedostatečný',
// };

// const formOptions = [
//     {
//         value: "0",
//         display: 'Odevzdané'
//     }, {
//         value: "1",
//         display: 'Neodevzdané'
//     }, {
//         value: "2",
//         display: 'Pošta'
//     }
// ]

const FilterOptions = (props: {
  parameters: SearchParametersType,
  // eslint-disable-next-line no-unused-vars
  setParams: (newParams: SearchParametersType) => void
}) => {

  function setFilterParam(name: string, value?: string) {
    // // eslint-disable-next-line @typescript-eslint/no-explicit-any
    // const newParams = ({...props.params}) as any;
    // newParams[name] = value;
    // setTimeout(props.setParams, 0, newParams);
  }

  function setSortBy(sortBy: DataOptions?) {
    // const newParams = {...props.params};
    // newParams.sortBy = sortBy;
    // newParams.reverse = reverse;
    // setTimeout(props.setParams, 0, newParams);
  }

  return (
    <div className="table_options_part">
      <div className="view_form">
        <label htmlFor="finished">Zobrazit:</label>

        <FilterOption label="Dokončené" options={statusOptions} selectedOption={props.parameters.filter.status} onChange={value => setFilterParam('status', value)} />
        <FilterOption label="Náhradní hodnocení" options={typeOptions} selectedOption={props.parameters.filter.type} onChange={value => setFilterParam('type', value)} />
        <FilterOption label="Úspěšně" options={successOptions} selectedOption={props.parameters.filter.success} onChange={value => setFilterParam('success', value)} />
        {/* <FilterOption label="Známky" options={markOptions} /> */}
        {/* <FilterOption label="Formulář" options={formOptions} /> */}

        <div id="view_delete_btn" className="form_row">
          <i className="fa-solid fa-x"></i>
        </div>
      </div>

      <div className="order_by_form">
        <label htmlFor="order_by">Seřadit podle:</label>
        <SortOption label="Třídy" sortBy={props.parameters.sort.by} reverse={props.parameters.reverse} onChange={setSortBy} />
      </div>
    </div>
  );
};

export default FilterOptions;
