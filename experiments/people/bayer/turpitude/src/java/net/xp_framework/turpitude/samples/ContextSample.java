package net.xp_framework.turpitude.samples;

import javax.script.*;
import net.xp_framework.turpitude.PHPEvalException;
import net.xp_framework.turpitude.PHPCompileException;

public class ContextSample {

   /**
    * default constructor
    */
    public ContextSample() {
    }

    /**
     * executes a script from a file
     */
    public void exec() {
        ScriptEngineManager mgr = new ScriptEngineManager();
        ScriptEngine eng = mgr.getEngineByName("turpitude");
        if (null == eng) {
            System.out.println("unable to find engine, please check classpath");
            return;
        }
        System.out.println("found Engine: " + eng.getFactory().getEngineName());
        ScriptContext ctx = eng.getContext();
        ctx.setAttribute("string", "stringval", ScriptContext.ENGINE_SCOPE);

        StringBuffer sb = new StringBuffer();
        sb.append("before Script\n");
        ctx.setAttribute("buffer", sb, ScriptContext.ENGINE_SCOPE);

        Object retval;
        try {
            retval = eng.eval(getSource());
        } catch(PHPCompileException e) {
            System.out.println("Compile Error:");
            e.printStackTrace();
            return;
        } catch(PHPEvalException e) {
            System.out.println("Eval Error:");
            e.printStackTrace();
            return;
        } catch(ScriptException e) {
            System.out.println("ScriptException caught:");
            e.printStackTrace();
            return;
        }
        if (null == retval)
            System.out.println("done evaluating, return value " + retval);
        else 
            System.out.println("done evaluating, return value " + retval.getClass() + " : " + retval);

        sb.append("after Script\n");
        System.out.println("Buffer val: \n" + sb.toString());
    }

    private static String getSource() {
        StringBuffer src = new StringBuffer();
        src.append("<?php \n");
        src.append("$turpenv = $_SERVER[\"TURP_ENV\"]; \n");
        src.append("$ctx = $turpenv->getScriptContext();");
        src.append("var_dump($ctx);");
        src.append("$attr = $ctx->getAttribute('(Ljava/lang/String;)Ljava/lang/Object;', 'string');");
        src.append("var_dump($attr);");
        src.append("$buffer = $ctx->getAttribute('(Ljava/lang/String;)Ljava/lang/Object;', 'buffer');");
        src.append("$buffer->append('(Ljava/lang/String;)Ljava/lang/StringBuffer;', 'Script Value\n');");
        src.append("?>"); 
        return src.toString();
    }

    /**
     * entry point
     */
    public static void main(String[] argv) {
        ContextSample cs = new ContextSample();
        cs.exec();
    }
 

}

