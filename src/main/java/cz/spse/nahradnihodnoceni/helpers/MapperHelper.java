package cz.spse.nahradnihodnoceni.helpers;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.fasterxml.jackson.datatype.hibernate6.Hibernate6Module;

public class MapperHelper {
    private static ObjectMapper mapper;

    public static ObjectMapper createMapper() {
        if (mapper != null)
            return mapper;

        var hibernateModule = new Hibernate6Module();
        hibernateModule.configure(Hibernate6Module.Feature.FORCE_LAZY_LOADING, false);
        hibernateModule.configure(Hibernate6Module.Feature.SERIALIZE_IDENTIFIER_FOR_LAZY_NOT_LOADED_OBJECTS, true);

        mapper = new ObjectMapper();
        mapper.registerModule(hibernateModule);
        return mapper;
    }
}
