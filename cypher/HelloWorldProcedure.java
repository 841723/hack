package com.cypher.neo4j.apoc;

import java.util.stream.Stream;
import org.neo4j.procedure.Description;
import org.neo4j.procedure.Mode;
import org.neo4j.procedure.Name;
import org.neo4j.procedure.Procedure;

public class HelloWorldProcedure {
   @Procedure(
      name = "custom.helloWorld",
      mode = Mode.READ
   )
   @Description("A simple hello world procedure")
   public Stream<HelloWorldProcedure.HelloWorldOutput> helloWorld(@Name("name") String name) {
      String greeting = "Hello, " + name + "!";
      return Stream.of(new HelloWorldProcedure.HelloWorldOutput(greeting));
   }

   public static class HelloWorldOutput {
      public String greeting;

      public HelloWorldOutput(String greeting) {
         this.greeting = greeting;
      }
   }
}
