import './FilterOptions.css';

import FilterOption from './FilterOption';
import SortOption from './SortOption';
import {SortByOptions, ExamDisplayRestrictions, Filter} from '../../models/ExamDisplayRestrictions';

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
  restrictions: ExamDisplayRestrictions,
  // eslint-disable-next-line no-unused-vars
  setRestrictions: (newParams: ExamDisplayRestrictions) => void
}) => {

  function setFilterParam(name: string, value?: string) {
    const newRestrictions = structuredClone(props.restrictions);
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-expect-error
    newRestrictions.filter[name] = value;
    props.setRestrictions(newRestrictions);
  }

  function clearFilterParams() {
    const newRestrictions = structuredClone(props.restrictions);
    newRestrictions.filter = new Filter();
    props.setRestrictions(newRestrictions);
  }

  function setSortBy(sortBy?: SortByOptions, reverse?: boolean) {
    const newRestrictions = structuredClone(props.restrictions);
    newRestrictions.sortBy = sortBy;
    newRestrictions.reverse = reverse;
    props.setRestrictions(newRestrictions);
  }

  return (
    <div className="table_options_part">
      <div className="view_form">
        <label htmlFor="finished">Zobrazit:</label>

        <FilterOption label="Dokončené" options={statusOptions} selectedOption={props.restrictions.filter.status} onChange={value => setFilterParam('status', value)} />
        <FilterOption label="Náhradní hodnocení" options={typeOptions} selectedOption={props.restrictions.filter.type} onChange={value => setFilterParam('type', value)} />
        <FilterOption label="Úspěšně" options={successOptions} selectedOption={props.restrictions.filter.success} onChange={value => setFilterParam('success', value)} />
        {/* <FilterOption label="Známky" options={markOptions} /> */}
        {/* <FilterOption label="Formulář" options={formOptions} /> */}

        <div id="view_delete_btn" className="form_row" onClick={clearFilterParams}>
          <i className="fa-solid fa-x"></i>
        </div>
      </div>

      <div className="order_by_form">
        <label htmlFor="order_by">Seřadit podle:</label>
        <SortOption label="Třídy" sortBy={props.restrictions.sortBy} reverse={props.restrictions.reverse} onChange={setSortBy} />
      </div>
    </div>
  );
};

export default FilterOptions;
