import React, { useContext } from 'react';
import './DashboardSearch.css';
import SearchParametersContext from '../../contexts/SearchParametersContext';

const DashboardSearch = () => {
  const searchParameters = useContext(SearchParametersContext);

  function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    event.stopPropagation();

    // Tohle by se v Reactu dělat nemělo ale je to nejideálnější řešení pokud víme,
    // že komponent DashboardSearch bude na stránce vždy jen jeden
    const element = document.getElementById('header_search')! as HTMLInputElement;

    setTimeout(props.onSubmit, 0, element.value);
  }

  return (
    <form className="header_search_part" onSubmit={handleSubmit}>
      <input type="search" name="header_search" id="header_search"
        placeholder="Vyhledávejte podle třídy, žáka nebo učitele" />
      <button type="submit" className="primar_btn">Vyhledat</button>
    </form>
  );
};

export default DashboardSearch;
