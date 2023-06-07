import React from 'react'
import './FilterOptions.css'

import FilterOption from './FilterOptionComponent'
import FilterParams, { sortByOptions } from '../../models/FilterParams'
import SortOption from './SortOptionComponent'

function propsToMapWithPrefix(prefix: string, props: string[]) {
    return Object.fromEntries(
        props.map(entry => [entry, `${prefix}${entry}`])
    )
}

const statusOptions = propsToMapWithPrefix("filter.status.", [
    "finished",
    "unfinished"
]);

const typeOptions = propsToMapWithPrefix("filter.type.", [
    "nahradni_hodnoceni",
    "opravna_zkouska"
]);

const successOptions = propsToMapWithPrefix("filter.success.", [
    "successful",
    "failed"
]);

const markOptions = {
    "1": '1 - Výborný',
    "2": '2 - Chvalitebný',
    "3": '3 - Dobrý',
    "4": '4 - Dostatečný',
    "5": '5 - Nedostatečný'
}

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
    params: FilterParams,
    setParams: (newParams: FilterParams) => void
}) => {

    function setParam(name: string, value?: string) {
        console.log(`changing ${name} to ${value}`);

        const newParams = Object.assign({}, props.params) as any;
        newParams[name] = value;
        setTimeout(props.setParams, 0, newParams);
    }

    function setSortBy(sortBy: sortByOptions, reverse?: boolean) {
        console.log(`changing sortBy`);

        const newParams = Object.assign({}, props.params);
        newParams.sortBy = sortBy;
        newParams.reverse = reverse;
        setTimeout(props.setParams, 0, newParams);
    }

    return (
        <div className="table_options_part">
            <div className="view_form">
                <label htmlFor="finished">Zobrazit:</label>
                
                <FilterOption label="Dokončené"             options={statusOptions}     selectedOption={props.params.status}    onChange={value => setParam("status",   value)} />
                <FilterOption label="Náhradní hodnocení"    options={typeOptions}       selectedOption={props.params.type}      onChange={value => setParam("type",     value)} />
                <FilterOption label="Úspěšně"               options={successOptions}    selectedOption={props.params.success}   onChange={value => setParam("success",  value)} />
                {/* <FilterOption label="Známky" options={markOptions} /> */}
                {/* <FilterOption label="Formulář" options={formOptions} /> */}

                <div id="view_delete_btn" className="form_row">
                    <i className="fa-solid fa-x"></i>
                </div>
            </div>

            <div className="order_by_form">
                <label htmlFor="order_by">Seřadit podle:</label>
                <SortOption label="Třídy" sortBy={props.params.sortBy} reverse={props.params.reverse} onChange={setSortBy} />
            </div>
        </div>
    )
}

export default FilterOptions
